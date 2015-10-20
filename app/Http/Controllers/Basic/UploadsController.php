<?php namespace App\Http\Controllers\Basic;
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 15/09/15
 * Time: 21:40
 */
use App\Http\Controllers\ApiController;
use Storage;
use Validator;
use Request;
use App\Models\Upload;
use App\Models\Comic;
use App\Models\Series;
use App\Models\ComicBookArchive;
use Rhumsaa\Uuid\Uuid;

class UploadsController extends ApiController {

    /**
     * @return mixed
     */
    public function index(Request $request){
        $currentUser = $this->getUser();

        $uploads = $currentUser->uploads()->paginate(env('paginate_per_page', 10))->toArray();

        $uploads['upload'] = $uploads['data'];
        unset($uploads['data']);

        return $this->respond($uploads);
    }


    /**
     * Display the specified upload.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id){
        $currentUser = $this->getUser();

        $upload = $currentUser->uploads()->find($id);

        if(!$upload){
            return $this->respondNotFound([
                'title' => 'Upload Not Found',
                'detail' => 'Upload Not Found',
                'status' => 404,
                'code' => ''
            ]);
        }

        return $this->respond([
            'upload' => [$upload]
        ]);
    }

    /**
     * Store a newly created upload in storage.
     *
     * @return Response
     */
    public function store(Request $request, Aws $Aws){
        $currentUser = $this->getUser();

        Validator::extend('valid_cba', function($attribute, $value, $parameters) {
            $acceptedMimetypes = array ('application/zip','application/rar','application/x-zip-compressed', 'multipart/x-zip','application/x-compressed','application/octet-stream','application/x-rar-compressed','compressed/rar','application/x-rar');
            $acceptedExtensionTypes = array ('zip', 'rar', 'cbz', 'cbr');
            if(in_array($value->getMimeType(),$acceptedMimetypes ) && in_array($value->getClientOriginalExtension(),$acceptedExtensionTypes)) {
                return true;
            }else{
                return false;
            }
        });

        Validator::extend('valid_uuid', function($attribute, $value, $parameters) {
            if(preg_match("/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i", $value)) {
                return true;
            } else {
                return false;
            }
        });

        Validator::extend('user_comics', function($attribute, $value, $parameters) {
            if($this->getUser()->comics()->find($value)){
                return false;
            }else{
                return true;
            }
        });

        $messages = [
            'file.valid_cba' => 'Not a valid File.',
            'comic_id.user_comics' => 'Not a valid Comic ID',
            'series_id.valid_uuid' => 'The :attribute field is not a valid ID.',
            'comic_id.valid_uuid' => 'The :attribute field is not a valid ID.',
            'file.required' => 'A file is required.'
        ];

        $validator = Validator::make(Request::all(), [
            'file' => 'required|valid_cba|between:1,150000',
            'exists' => 'required|boolean',
            'series_id' => 'required|valid_uuid',
            'comic_id' => 'required|valid_uuid|user_comics',
            'series_title' => 'required',
            'series_start_year' => 'required|numeric',
            'comic_issue' => 'required|numeric'
        ], $messages);

        if ($validator->fails()){
            $pretty_errors = array_map(function($item){
                return [
                    'title' => 'Missing Required Field Or Incorrectly Formatted Data',
                    'detail' => $item,
                    'status' => 400,
                    'code' => ''
                ];
            }, $validator->errors()->all());

            return $this->respondBadRequest($pretty_errors);
        }

        $file = Request::file('file');
        $fileHash = hash_file('md5', $file->getRealPath());
        $match_data = Request::except('file');

        //Write Upload to DB
        $upload = new Upload;
        $upload->file_original_name = $file->getClientOriginalName();
        $upload->file_size = $file->getSize();
        $upload->file_original_file_type = $file->getClientOriginalExtension();
        $upload->user_id = $currentUser->id;
        $upload->match_data = json_encode($match_data);
        $upload->save();

        $newFileName = Uuid::uuid4()->toString().".".$file->getClientOriginalExtension();

        $cba = ComicBookArchive::where('comic_book_archive_hash', '=', $fileHash)->first();
        $process_cba = false;
        //If not write an entry for one to the DB and send the file to S3
        if(!$cba){//Upload not found so send file to S3
            Storage::disk(env('user_uploads', 'local_user_uploads'))->put($newFileName, File::get($file));
            //Storage::disk(env('user_uploads', 'local_user_uploads'))->put($newFileName, File::get($file));//TODO: Make sure right AWS S3 ACL is used in production
            //Storage::disk(env('user_uploads', 'local_user_uploads'))->getDriver()->getAdapter

            //$s3 = AWS::createClient('s3');
            //$s3->pu

            //$permanent_location = "https://s3".env('AWS_REGION', 'us-east-1').".amazonaws.com/".env('AWS_S3_Uploads')."/".$newFileName; //TODO: This ideally needs to something returned from Laravel's upload
            $permanent_location = $this->getFileUrl(
                Storage::disk(env('user_uploads', 'local_user_uploads'))->getDriver()->getAdapter,
                $newFileName
            );
            //create cba
            $cba = $this->createComicBookArchive($upload->id, $fileHash, $permanent_location);
            $process_cba = true;
        }

        //check if series exists, if not create one
        $series = User::find($this->currentUser->id)->first()->series()->find($match_data['series_id']);

        if(!$series){//create
            $series = $this->createSeries($match_data);
        }

        $comic_info = [
            'comic_issue' => $match_data['comic_issue'],
            'comic_id' => $match_data['comic_id'],
            'series_id' => $series->id,
            'comic_writer' => 'Unknown',
            'comic_book_archive_id' => $cba->id
        ];

        $comic = $this->createComic($comic_info);

        //invoke lambda
        /*if($process_cba) {
            $s3 = AWS::createClient('s3');
            $s3TempLink = $s3->getObjectUrl(env('AWS_S3_Uploads'), $newFileName, '+10 minutes');

            $lambda = AWS::get('Lambda');
            $lambda->invokeAsync([
                'FunctionName' => env('LAMBDA_FUNCTION_NAME'),
                'InvokeArgs' => json_encode([
                    "api_base" => url(),
                    "api_version" => 'v'.env('APP_API_VERSION'),//TODO: This should be processor.
                    "environment" => env('APP_ENV'),
                    "fileLocation" => $s3TempLink,
                    "cba_id" => $cba->id
                ]),
            ]);
        }*/


        return $this->respondCreated([
            'upload' => [$upload]
        ]);

    }


}
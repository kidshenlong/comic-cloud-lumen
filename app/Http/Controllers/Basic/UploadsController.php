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
use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\Comic;
use App\Models\Series;
use App\Models\User;
use App\Models\ComicBookArchive;
use Rhumsaa\Uuid\Uuid;
use Aws\Laravel\AwsFacade as AWS;

class UploadsController extends ApiController {

    protected $request;

    public function __construct(Request $request){
        $this->request = $request;
    }

    /**
     * @return mixed
     */
    public function index(){
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
            'upload' => $upload
        ]);
    }

    /**
     * Store a newly created upload in storage.
     *
     * @return Response
     */
    public function store(){

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
            if(Comic::find($value)){
                return false;
            }
            return true;
        });

        Validator::extend('user_series', function($attribute, $value, $parameters) {
            //Check if the series is owned by someone else
            $series = Series::find($value);
            if($series){
                if($series->user_id == $this->getUser()->id){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
        });

        $messages = [
            'file.valid_cba' => 'Not a valid File.',
            'comic_id.user_comics' => 'Not a valid Comic ID',
            'series_id.user_series' => 'Not a valid Series ID',
            'series_id.valid_uuid' => 'The :attribute field is not a valid ID.',
            'comic_id.valid_uuid' => 'The :attribute field is not a valid ID.',
            'file.required' => 'A file is required.'
        ];

        $validator = Validator::make($this->request->all(), [
            'file' => 'required|valid_cba|between:1,150000',
            'series_id' => 'required|valid_uuid|user_series',
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

        $file = $this->request->file('file');
        $fileHash = hash_file('md5', $file->getRealPath());
        $match_data = $this->request->except('file');

        $upload = (new Upload)->create([
            "id" => Uuid::uuid4()->toString(),
            "file_original_name" => $file->getClientOriginalName(),
            "file_size" => $file->getSize(),
            "file_original_file_type" => $file->getClientOriginalExtension(),
            "user_id" => $currentUser->id,
            "match_data" => json_encode($match_data)
        ]);

        $newFileName = Uuid::uuid4()->toString().".".$file->getClientOriginalExtension();

        $cba = ComicBookArchive::where('comic_book_archive_hash', '=', $fileHash)->first();

        $process_cba = false;

        if(!$cba){

            Storage::disk('user_uploads')->put($newFileName, file_get_contents($file));//TODO: Make sure right AWS S3 ACL is used in production

            $permanent_location = getFileUrl("s3", $newFileName);

            $cba = (New ComicBookArchive)->create([
                "upload_id" => $upload->id,
                "comic_book_archive_hash" => $fileHash,
                "comic_book_archive_status" => 0,
                "comic_book_archive_permanent_location" => $permanent_location
            ]);

            $process_cba = true;
        }

        $series = $currentUser->series()->find($match_data['series_id']);

        if(!$series){
            $series = (New Series)->create([
                "id" => $match_data['series_id'],
                "series_title" => $match_data['series_title'],
                "series_start_year" => $match_data['series_start_year'],
                "series_publisher" => 'Unknown',
                "user_id" => $currentUser->id
            ]);
        }

        $comic_info = [
            'comic_issue' => $match_data['comic_issue'],
            'comic_id' => $match_data['comic_id'],
            'series_id' => $series->id,
            'comic_writer' => 'Unknown',
            'comic_book_archive_id' => $cba->id
        ];

        $comic = (New Comic)->create([
            "id" => $comic_info['comic_id'],
            "comic_issue" => $comic_info['comic_issue'],
            "comic_writer" => $comic_info['comic_writer'],
            "comic_book_archive_contents" => (($cba->comic_book_archive_contents ? $cba->comic_book_archive_contents : '')),
            "user_id" => $currentUser->id,
            "series_id" => $comic_info['series_id'],
            "comic_book_archive_id" => $cba->id
        ]);

        //invoke lambda
        if($process_cba) {

            $temporary_location = getFileUrl("s3", $newFileName, "+10 minutes");

            /*$lambda = AWS::get('Lambda');
            $lambda->invokeAsync([
                'FunctionName' => env('LAMBDA_FUNCTION_NAME'),
                'InvokeArgs' => json_encode([
                    "api_base" => url(),
                    "api_version" => 'v'.env('APP_API_VERSION'),//TODO: This should be processor.
                    "environment" => env('APP_ENV'),
                    "fileLocation" => $temporary_location,
                    "cba_id" => $cba->id
                ]),
			]);*/
        }

        return $this->respondCreated([
            'upload' => $upload
        ]);

    }


}

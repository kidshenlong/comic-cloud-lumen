<?php namespace App\Http\Controllers\Basic;
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 15/09/15
 * Time: 21:40
 */
use App\Http\Controllers\ApiController;
use Validator;
use Request;
use App\Models\Upload;
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
    public function store(Request $request){
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
            'comic_id.valid_uuid' => 'The :attribute field is not a valid ID.'
        ];

        $validator = Validator::make(Request::all(), [
            /*'file' => 'required|valid_cba|between:1,150000',
            'exists' => 'required|boolean',
            'series_id' => 'required|valid_uuid',
            'comic_id' => 'required|valid_uuid|user_comics',
            'series_title' => 'required',
            'series_start_year' => 'required|numeric',
            'comic_issue' => 'required|numeric',*/
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
        $newFileNameWithNoExtension = $upload->file_random_upload_id = Uuid::uuid4()->toString();
        $upload->file_upload_name = $newFileName = $newFileNameWithNoExtension . '.' . $file->getClientOriginalExtension();
        $upload->file_original_file_type = $file->getClientOriginalExtension();
        $upload->user_id = $currentUser->id;
        $upload->match_data = json_encode($match_data);
        $upload->save();

        return $this->respondCreated([
            'upload' => [$upload]
        ]);



    }


}
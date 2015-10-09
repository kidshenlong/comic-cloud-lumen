<?php namespace App\Http\Controllers\Basic;
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 15/09/15
 * Time: 21:40
 */
use App\Http\Controllers\ApiController;
use Request;
use App\Models\Upload;

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
}
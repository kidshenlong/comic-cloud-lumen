<?php namespace App\Http\Controllers\Basic;
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 15/09/15
 * Time: 21:40
 */
use App\Http\Controllers\ApiController;

class UploadsController extends ApiController {

    /**
     * @return mixed
     */
    public function index(){
        return $this->respondCreated(['lol']);
    }
}
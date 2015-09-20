<?php namespace App\Http\Controllers;
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 15/09/15
 * Time: 21:42
 */

use App\Http\Requests;

use App\Models\User;
use LucaDegasperi\OAuth2Server\Authorizer;
use Symfony\Component\HttpFoundation\Response as IlluminateResponse;

class ApiController extends Controller {

    /**
     * @var int
     */
    protected $statusCode = 200;
    protected $statusMessage = 'success';
    protected $message = null;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return User::find(Authorizer::getResourceOwnerId());
    }

    /**
     * @param mixed $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
    /**
     * @param string $message
     * @return mixed
     */
    public function respondNotFound($message = 'Not Found'){
        return $this->setStatusCode(IlluminateResponse::HTTP_NOT_FOUND)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function respondInternalError($message = 'Internal Error'){
        return $this->setStatusCode(IlluminateResponse::HTTP_INTERNAL_SERVER_ERROR)->respondWithError($message);
    }

    /**
     * @param string $message
     * @return mixed
     */
    public function respondBadRequest($message = 'Internal Error'){
        return $this->setStatusCode(IlluminateResponse::HTTP_BAD_REQUEST)->respondWithError($message);
    }

    /**
     * @param $message
     * @return mixed
     */
    protected function respondSuccessful($message)
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_OK)->respond($message);
    }
    /**
     * @param $message
     * @return mixed
     */
    protected function respondCreated($message)
    {
        return $this->setStatusCode(IlluminateResponse::HTTP_CREATED)->respond($message);
    }

    public function respondNoContent(){

        return $this->setStatusCode(IlluminateResponse::HTTP_NO_CONTENT)->respond();
    }

    public function respondUnauthorised($message = "Unauthorised Request"){

        return $this->setStatusCode(IlluminateResponse::HTTP_UNAUTHORIZED)->respond($message);
    }
    /**
     * @param $errors_object
     * @internal param $message
     * @return mixed
     */
    public function respondWithError($errors_object){
        return $this->respond([
            'errors' => $errors_object
        ]);
    }
    /**
     * @param $data
     * @param array $headers
     * @return mixed
     */
    public function respond($data = [], $headers = []){
        return response()->json($data, $this->getStatusCode(), $headers);
    }

}
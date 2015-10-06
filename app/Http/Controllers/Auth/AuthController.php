<?php namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Models\User;
use Validator;
use Authorizer;

class AuthController extends ApiController {

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $request
     * @return User
     */
    public function create(Request $request)
    {
        $request = $request->json()->all();

        $validator = Validator::make($request, [
            'username' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($validator->fails()){
            $pretty_errors = array_map(function($item){
                return [
                    'title' => 'Missing Required Field',
                    'detail' => $item,
                    'status' => 400,
                    'code' => ''
                ];
            }, $validator->errors()->all());

            return $this->respondBadRequest($pretty_errors);
        }

        User::create([
            'username' => $request['username'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
        ]);

        return $this->respondCreated([[
            'title' => 'Registration Successful',
            'detail' => 'Registration Successful',
            'status' => 201,
            'code' => ''
        ]]);
    }

    public function createToken(Request $request){
        //isJson()
        //dd($request->isJson());
        $json = $request->json()->all();

        $request = new Request;

        $request->request->replace($json);

        Authorizer::setRequest($request);

        return $this->respond(Authorizer::issueAccessToken());
    }

}
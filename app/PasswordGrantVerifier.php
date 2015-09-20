<?php namespace App;
/**
 * Created by PhpStorm.
 * User: Michael
 * Date: 20/09/15
 * Time: 00:42
 */

use Illuminate\Support\Facades\Auth;

class PasswordGrantVerifier
{
    public function verifyBasic($username, $password)
    {
        $credentials = [
            'username'    => $username,
            'password' => $password,
        ];

        if (Auth::once($credentials)) {
            return Auth::user()->type == "basic" ? Auth::user()->id : false;
        } else {
            return false;
        }
    }

    public function verifyAdmin($username, $password)
    {
        $credentials = [
            'username'    => $username,
            'password' => $password,
        ];

        if (Auth::once($credentials)) {
            return Auth::user()->type == "admin" ? Auth::user()->id : false;
        } else {
            return false;
        }
    }
}
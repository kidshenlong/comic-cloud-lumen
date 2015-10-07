<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/status', function () use ($app) {
    return [
        'status' => 'OK'
    ];
});

$app->group(['namespace' => 'App\Http\Controllers\Auth', 'prefix' => 'auth'], function($app) {
    $app->post('register', 'AuthController@create');
    $app->post('access_token', 'AuthController@createToken');
});



$app->group(['namespace' => 'App\Http\Controllers\Basic', 'prefix' => 'v'.env('APP_VERSION', '0.1'), 'middleware' => 'oauth'], function ($app) {
    $app->get('uploads', 'UploadsController@index');
    $app->post('uploads', 'UploadsController@store');
    $app->get('uploads/{id}', 'UploadsController@show');
});


$app->group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin'], function ($app) {


});


$app->group(['namespace' => 'App\Http\Controllers\Processor', 'prefix' => 'processor'], function ($app) {


});
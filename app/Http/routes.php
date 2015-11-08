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
    $app->get('uploads/{upload_id}', 'UploadsController@show');

    $app->get('images/{image_slug}', 'ComicImagesController@show');
    $app->get('images/{image_slug}/{size}', 'ComicImagesController@show');

    $app->get('series', 'SeriesController@index');
    $app->get('series/{series_id}', 'SeriesController@show');
    $app->post('series', 'SeriesController@store');
    $app->put('series/{series_id}', 'SeriesController@update');
    $app->delete('series/{series_id}','SeriesController@destroy');
    $app->get('series/{series_id}/matches', 'SeriesController@showMatchData');
    $app->get('series/{series_id}/comics', 'SeriesController@showRelatedComics');

    $app->get('comics', 'ComicsController@index');
    $app->get('comics/{comic_id}', 'ComicsController@show');
    $app->put('comics/{comics_id}', 'ComicsController@update');
    $app->delete('comics/{comics_id}','ComicsController@destroy');
    $app->get('comics/{comic_id}/series', 'ComicsController@showRelatedSeries');
    $app->get('comics/{comic_id}/matches', 'ComicsController@showMatchData');

});


$app->group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin'], function ($app) {


});


$app->group(['namespace' => 'App\Http\Controllers\Processor', 'prefix' => 'processor'], function ($app) {


});
<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/creatForms', function () {
    return view('creatForms');
});

Route::post('api/createForm', array('uses' => 'AddForms@addForms'));
Route::get('api/addData', array('uses' => 'AddForms@addData'));
Route::get('api/getForms', array('uses' => 'AddForms@getForms'));
Route::get('api/showFormData', array('uses' => 'AddForms@showFormData'));
Route::post('api/fileuploader', array('uses' => 'AddForms@fileUploader'));
Route::get('/test', function () {
    return view('test');
});

Route::auth();

Route::get('/home', 'HomeController@index');

Route::auth();

Route::get('/home', 'HomeController@index');

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::group(['middleware' => ['cors']], function ($router) {
         //Add you routes here, for example:
        //Route::apiResource('/posts','PostController');
		Route::post('/user/signup', 'UserController@signup')->name('signup');	
		Route::post('/user/login', 'UserController@login')->name('login');	
		Route::post('/user/search', 'UserController@search')->name('search');	
		Route::post('/user/chats', 'UserController@chats')->name('chats');	
		Route::post('/user/chats/{id}', 'UserController@chats')->name('chats');	
		Route::post('/message/send', 'MessageController@save')->name('save');	
    });

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

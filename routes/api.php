<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'Auth\PassportController@login');
Route::post('register', 'Auth\PassportController@register');

/*
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
*/

Route::middleware('auth:api')->group(function () {
    Route::get('user', 'Auth\PassportController@details');
    Route::resource('categories', 'API\CategoryController');
    Route::resource('posts', 'API\PostController');
});

Route::resource('post-categories', 'API\CategoryController');


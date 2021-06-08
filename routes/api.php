<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Auth::routes();

/**
 * 
 * Rutas para clientes
 * 
 */
Route::resource('clients', 'ClientController')->except(['show']);
Route::get('check-clients', 'ClientController@checkSecret');
Route::get('check-users', 'HomeController@checkUserAuthentication');

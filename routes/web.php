<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::group(['namespace'=>'Laradevsbd\Zkteco\Http\Controllers'],function (){
    Route::get('zkteco','ZktecoController@index');
});

\Illuminate\Support\Facades\Route::get('test',function (){
   return "ok";
});
Route::get('/', function () {
    return view('welcome');
});

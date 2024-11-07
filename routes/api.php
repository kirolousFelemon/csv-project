<?php

use Illuminate\Http\Request;
use App\Http\Controllers\UserController;
use Laradevsbd\Zkteco\Http\Controllers\ZktecoController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('tests', [UserController::class, 'tests']);
Route::get('export', [UserController::class, 'export']);
Route::get('export-new', [UserController::class, 'exportNew']);
Route::get('zkteco', [ZktecoController::class ,'index']);



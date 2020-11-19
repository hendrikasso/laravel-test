<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\WorkShift;

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

Route::get('workshifts', 'App\Http\Controllers\WorkShiftController@list');
Route::get('workshifts/{workShift}', 'App\Http\Controllers\WorkShiftController@show');
Route::post('workshifts', 'App\Http\Controllers\WorkShiftController@create');
Route::put('workshifts/{workShift}', 'App\Http\Controllers\WorkShiftController@update');
Route::delete('workshifts/{workShift}', 'App\Http\Controllers\WorkShiftController@delete');

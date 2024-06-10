<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CkassaController;
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

Route::post('event', [ApiController::class, 'event']); 
Route::post('openGate', [ApiController::class, 'openGate']); 
Route::post('closeGate', [ApiController::class, 'closeGate']); 
Route::post('ckassa/callback', [CkassaController::class, 'callback']);
Route::get('ckassa/status', [CkassaController::class, 'status']);
Route::get('test_ctreate_transport', [ApiController::class, 'test_createTransport']);
Route::get('getLogs', [ApiController::class, 'getLogs']);
Route::get('test/email', [ApiController::class, 'testSendEmail']);
Route::get('sigur/event2', [ApiController::class, 'testSigurEvent']);
Route::get('sigur/event', [ApiController::class, 'sigurEventNumber']);
Route::get('sigur/getchannels', [ApiController::class, 'sigurGetChannels']);
// Route::group(['middleware' => 'auth:sanctum'], function () {
//     Route::post('event', [ApiController::class, 'event']); 
// });
Route::get('search/transport/{searchText}', [ApiController::class, 'searchTransport']);
Route::get('search/tenant/{searchText}', [ApiController::class, 'searchTenant']);
Route::get('getTypeTransport', [ApiController::class, 'getTypeTransport']);
Route::post('createPass', [ApiController::class, 'createPass']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


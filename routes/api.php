<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

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
Route::get('test_ctreate_transport', [ApiController::class, 'test_createTransport']);
Route::get('ffmpeg', [ApiController::class, 'ffmpeg']);
Route::get('test_234', [ApiController::class, 'test_234']);

// Route::group(['middleware' => 'auth:sanctum'], function () {
//     Route::post('event', [ApiController::class, 'event']); 
// });



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

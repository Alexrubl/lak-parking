<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CkassaController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/ckassa', [CkassaController::class, 'show']);

// Route::get('/getOrder', [CkassaController::class, 'getOrder']);
Route::get('getchannels', [ApiController::class, 'sigurGetChannels']);  
Route::post('pay/ckassa', [CkassaController::class, 'callback']);


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\CkassaController;
use Laravel\Nova\Notifications\NovaNotification;

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

Route::get('/abc', function() {
    $users = \App\Models\User::all()->filter(function ($value, $key) {
        return $value->isRoot();
    });

    foreach ($users as $key => $user) {
        $user->notify(NovaNotification::make()
            ->message('Сообщение')
            ->type('error')
        );
    }

});




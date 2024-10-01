<?php

use App\Http\Controllers\ApiController;
use App\Models\TypeTransport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Card API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your card. These routes
| are loaded by the ServiceProvider of your card. You're free to add
| as many additional routes to this file as your card may require.
|
*/

// Route::get('/endpoint', function (Request $request) {
//     //
// });

// Route::get('/api/getTypeTransport', function (Request $request) {
//     info('kjhjhjhj');
// });

Route::get('getTypeTransport', function (Request $request) {
    return response()->json(TypeTransport::all('id', 'name'), 200);
});

Route::get('search/transport/{searchText}', [ApiController::class, 'searchTransport']);
Route::get('search/tenant/{searchText}', [ApiController::class, 'searchTenant']);
Route::post('createPass', [ApiController::class, 'createPass']);

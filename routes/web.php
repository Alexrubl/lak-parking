<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\CkassaController;
use Carbon\Carbon;
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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/ckassa', [CkassaController::class, 'show']);

// Route::get('/getOrder', [CkassaController::class, 'getOrder']);
Route::get('getchannels', [ApiController::class, 'sigurGetChannels']);
Route::post('pay/ckassa', [CkassaController::class, 'callback']);

Route::get('/abc', function () {
    info('abc');
    \App\Jobs\DemoJob::dispatch()->onQueue('transports');
});

Route::get('/abc2', function () {
    $data = [
        'apikey' => \App\Models\Controller::first()->apikey,
        'request_id' => Carbon::now()->format('Ymdhms'),
        'ev_date' => Carbon::now()->format('Y.m.d H:m:s'),
    ];
    foreach (\App\Models\Transport::limit(5)->get() as $key => $transport) {
        $week = '';
        if ($transport->week) {
            foreach ($transport->week as $key => $value) {
                $week .= ($value == 1) ? '1' : '0';
            }
        } else {
            $week = '0000000';
        }
        $data['items'][] = [
            'create' => [
                'parent' => [
                    'name' => $transport->tenant->name,
                    'id' => $transport->tenant->id,
                    'access' => $transport->balance <= 0 ? 0 : 1,
                ],
                'plate' => $transport->number,
                'fio' => $transport->driver,
                'access' => intval($transport->access),
                'authentication' => $transport->type_auth,
                'tid' => $transport->tid(),
            ],
            'access' => [
                'time_limit' => $transport->restrictions ? intval($transport->time_limit) : 0,
                'week' => $transport->restrictions ? $week : '1111111',
                'time_interval' => $transport->restrictions ? str_replace([':'], '', isset($transport->fromTime) ? $transport->fromTime : '00:00').'-'.str_replace([':'], '', isset($transport->toTime) ? $transport->toTime : '23:59') : '0000-2359',
                'date_interval' => $transport->restrictions ? (isset($transport->fromDate) ? Carbon::parse($transport->fromDate)->format('Ymd') : Carbon::now()->format('Ymd')).'-'.(isset($transport->toDate) ? Carbon::parse($transport->toDate)->format('Ymd') : '21191231') : Carbon::now()->format('Ymd').'-21191231',
            ],
        ];

        info($data);

    }
    dd(json_encode($data));
});

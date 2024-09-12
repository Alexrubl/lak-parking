<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();

Schedule::job(new \App\Jobs\InsideTransportVerify)->hourly();


#Очистка пакетов
Schedule::command('queue:prune-batches --hours=48')->daily();
Schedule::command('queue:flush --hours=48')->daily();

<?php

namespace App\Observers;

use App\Http\Controllers\ApiController as Api;
use App\Models\Transport;
use Illuminate\Support\Facades\Auth;
use App\Models\Journal;

class TransportObserver
{
    /**
     * Handle the Transport "created" event.
     */
    public function created(Transport $transport): void
    {
        try {
            info('created Transport');
            if (Auth::check()) {
                $journal = new Journal;
                $journal->text = 'Создание траспорта '. $transport->number;
                $journal->user_id = Auth::user()->id;
                $journal->save();
            }
            // $api = new Api;
            // $api->sendNewTransportToControllers($transport);
            \App\Jobs\SendTransportInfoToController::dispatch($transport)->onQueue('transports');
        } catch (\Throwable $th) {
            info($th->getMessage());
        }
    }

    /**
     * Handle the Transport "updated" event.
     */
    public function updated(Transport $transport): void
    {
        try {
            info('updated Transport');
            if (Auth::check()) {
                $journal = new Journal;
                $journal->text = 'Обновление траспорта '. $transport->number;
                $journal->user_id = Auth::user()->id;
                $journal->save();
            }
            // $api = new Api;
            // $api->sendNewTransportToControllers($transport);
            \App\Jobs\SendTransportInfoToController::dispatch($transport)->onQueue('transports')->delay(now()->addSeconds(2));
        } catch (\Throwable $th) {
            info($th->getMessage());
        }
    }

    /**
     * Handle the Transport "deleted" event.
     */
    public function deleted(Transport $transport): void
    {
        if (Auth::check()) {
            $journal = new Journal;
            $journal->text = 'Удаление траспорта '. $transport->number;
            $journal->user_id = Auth::user()->id;
            $journal->save();
        }
        $transport->access = 0;
        $transport->save();
    }

    /**
     * Handle the Transport "restored" event.
     */
    public function restored(Transport $transport): void
    {
        //
    }

    /**
     * Handle the Transport "force deleted" event.
     */
    public function forceDeleted(Transport $transport): void
    {
        info('forcedelete');
        if (Auth::check()) {
            $journal = new Journal;
            $journal->text = 'Полное удаление траспорта '. $transport->number;
            $journal->user_id = Auth::user()->id;
            $journal->save();
        }
        $transport->forceDeleteQuietly();
    }
}

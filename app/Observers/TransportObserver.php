<?php

namespace App\Observers;

use App\Models\Transport;
use App\Http\Controllers\ApiController as Api;

class TransportObserver
{
    /**
     * Handle the Transport "created" event.
     */
    public function created(Transport $transport): void
    {
        
        try {
            $api = new Api;
            $api->sendNewTransportToControllers($transport);
            info('created Transport');
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
            $api = new Api;
            $api->sendNewTransportToControllers($transport);
            info('updated Transport');
        } catch (\Throwable $th) {
            info($th->getMessage());
        } 
    }

    /**
     * Handle the Transport "deleted" event.
     */
    public function deleted(Transport $transport): void
    {
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
        //
    }
}

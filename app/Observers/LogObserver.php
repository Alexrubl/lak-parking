<?php

namespace App\Observers;

class LogObserver
{
        /**
     * Handle the Transport "created" event.
     */
    public function created(Transport $transport): void
    {
        

    }

    /**
     * Handle the Transport "updated" event.
     */
    public function updated(Transport $transport): void
    {
 
    }

    /**
     * Handle the Transport "deleted" event.
     */
    public function deleted(Transport $transport): void
    {

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

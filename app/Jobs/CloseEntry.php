<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController as Api;
use App\Models\Controller;

class CloseEntry implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $controller;
    public $req;
    /**
     * Create a new job instance.
     */
    public function __construct($controller)
    {
        $this->controller = $controller;
    }

    /** 
     * Execute the job.
     */
    public function handle(): void
    {       
        sleep($this->controller->pausa);
        $api = new Api;
        info('Закрывем проезд автоматически после паузы '.$this->controller->pausa. ' сек.');
        $api->closeGate(null, $this->controller);
    }
}

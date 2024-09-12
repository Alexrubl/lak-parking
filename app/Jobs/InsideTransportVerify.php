<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Transport;
use App\Models\History;
Use Carbon\Carbon;

class InsideTransportVerify implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        \Log::info("Job InsideTransportVerify run");
        $transports = Transport::inside()->get();
        info(collect($transports));
        foreach ($transports as $key => $transport) {
            $last_history_entry = History::where('transport_id', $transport->id)->orderby('id', 'desc')->first();
            if (isset($last_history_entry) && Carbon::Now() > Carbon::parse($last_history_entry->created_at)->addHours(12)) {
                info($transport->name . ' - '. $transport->number.' force inside out');
                $transport->inside = 0;
                if ($transport->guest) { // Если транспорт гостевой закрываем доступ
                    $transport->access = 0;
                }
                $transport->save();
            } elseif (!isset($last_history_entry)) { # если в истории нет транспорта, то убираем что он на территории
                $transport->inside = 0;
                $transport->save();
            }
        }
    }
}

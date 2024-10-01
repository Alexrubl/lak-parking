<?php

namespace App\Jobs;

use App\Models\History;
use App\Models\Transport;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        \Log::info('Job InsideTransportVerify run');
        $transports = Transport::inside()->get();
        foreach ($transports as $key => $transport) {
            $last_history_entry = History::where('transport_id', $transport->id)->whereNotNull('direction')->orderby('id', 'desc')->first();
            info_d($last_history_entry);
            if (isset($last_history_entry) && Carbon::Now() > Carbon::parse($last_history_entry->created_at)->addHours(12)) {
                info_d($transport->name.' - '.$transport->number.' force inside out');
                $transport->inside = 0;
                if ($transport->guest) { // Если транспорт гостевой закрываем доступ
                    $transport->access = 0;
                }
                $transport->save();
                if ($transport->guest) { // Если транспорт гостевой закрываем доступ
                    $transport->delete();
                }
            } elseif (!isset($last_history_entry)) { // если в истории нет транспорта, то убираем что он на территории
                $transport->inside = 0;
                $transport->save();
            }
        }
    }
}

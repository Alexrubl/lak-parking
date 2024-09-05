<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Controller;
use App\Models\Transport;
use Illuminate\Support\Carbon;
use DateTime;
use Laravel\Nova\Notifications\NovaNotification;
use App\Models\User;

class SendTransportInfoToController implements ShouldQueue
{
    use Queueable;

    /**
     * Количество попыток выполнения задания.
     *
     * @var int
     */
    public $tries = 5;

    public $timeout = 15;

    /**
     * Задать временной предел попыток выполнить задания.
     *
     * @return \DateTime
     */
    public function retryUntil(): DateTime
    {
        return now()->addMinutes(5);
    }

    /**
     * Максимальное количество разрешенных необработанных исключений.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
    * Рассчитать количество секунд ожидания перед повторной попыткой выполнения задания.
    *
    * @return array<int, int>
    */
    public function backoff(): array
    {
        return [10, 90, 180];
    }

    protected $transport;

    /**
     * Create a new job instance.
     */
    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {        
        $controllers = Controller::all();
        $transport = $this->transport;
        foreach ($controllers as $key => $controller) {            
            if (!$controller->active)  continue;
            info('SendTransportInfoToController: отправляем транспорт "'. $transport->number .'" на контроллер "'.$controller->name.'"');
            echo 'SendTransportInfoToController: отправляем транспорт "'. $transport->number .'" на контроллер "'.$controller->name.'"';
            $week = '';
            if ($transport->week) {
                foreach ($transport->week as $key => $value) {
                    $week .= ($value == 1) ? '1':'0';
                }
            } else {
                $week = '0000000';
            }
            $data = [
                'apikey' => $controller->apikey,
                'request_id' => Carbon::now()->format('Ymdhms'),
                'ev_date' => Carbon::now()->format('Y.m.d H:m:s'),
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
                    'tid' => $transport->tid()
                ],
                'access' => [
                    'time_limit' => $transport->restrictions ? intval($transport->time_limit) : 0,
                    'week' => $transport->restrictions ? $week : '1111111',
                    'time_interval' => $transport->restrictions ? str_replace([':'], '', isset($transport->fromTime)? $transport->fromTime : '00:00') .'-'.str_replace([':'], '', isset($transport->toTime)? $transport->toTime : '23:59') : '0000-2359',
                    'date_interval' => $transport->restrictions ? (isset($transport->fromDate) ? Carbon::parse($transport->fromDate)->format('Ymd') : Carbon::now()->format('Ymd')).'-'. (isset($transport->toDate) ? Carbon::parse($transport->toDate)->format('Ymd') : '21191231') : Carbon::now()->format('Ymd').'-21191231',
                ]
            ];

            // info($data);
            // dd();

            $curl = curl_init();

            curl_setopt_array($curl, [
            //CURLOPT_PORT => "8082",
            CURLOPT_URL => $controller->ip. '/api/plate/srv',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data), //http_build_query($data),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                info("cURL Error #: " . $err);
                $this->release(60);
            } else {
                //info($response);
            }
        }
        //info(json_encode($data));
    }

    /**
     * Обработать провал задания.
     */
    public function failed(?Throwable $exception): void
    {
        // Отправляем пользователю уведомление об ошибке и т.д.
        $users = \App\Models\User::all()->filter(function ($value, $key) {
            return $value->isRoot();
        });
        
        foreach ($users as $key => $user) {
            $user->notify(NovaNotification::make()
                ->message('Ошибка доставки данный контроллеру: '.$exception->getMessage())
                ->type('error')
            );
        }
    }
}

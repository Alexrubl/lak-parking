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
use Throwable;

class SendTransportInfoToController implements ShouldQueue
{
    use Queueable;

    /**
     * Количество попыток выполнения задания.
     *
     * @var int
     */
    public $tries = 3;

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
    public function backoff(): int | array
    {
        return 10;
    }

    protected $transport;
    protected $tenant;

    /**
     * Create a new job instance.
     */
    public function __construct(Transport $transport)
    {
        $this->transport = $transport->withoutRelations();
        $this->tenant = $transport->tenant->withoutRelations();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $controllers = Controller::all(['apikey', 'active' ,'id', 'name', 'ip']);
        foreach ($controllers as $key => $controller) {
            if (!$controller->active)  continue;
            info('SendTransportInfoToController: отправляем транспорт "'. $this->transport->number .'" на контроллер "'.$controller->name.'"');
            echo 'SendTransportInfoToController: отправляем транспорт "'. $this->transport->number .'" на контроллер "'.$controller->name.'"' . PHP_EOL;
            $week = '';
            if ($this->transport->week) {
                foreach ($this->transport->week as $key => $value) {
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
                        'name' => $this->tenant->name,
                        'id' => $this->tenant->id,
                        'access' => $this->transport->balance <= 0 ? 0 : 1,
                    ],
                    'plate' => $this->transport->number,
                    'fio' => $this->transport->driver,
                    'access' => intval($this->transport->access),
                    // 'authentication' => $this->transport->type_auth,
                    // 'tid' => $this->transport->tid()
                ],
                'access' => [
                    'time_limit' => $this->transport->restrictions ? intval($this->transport->time_limit) : 0,
                    'week' => $this->transport->restrictions ? $week : '1111111',
                    'time_interval' => $this->transport->restrictions ? str_replace([':'], '', isset($this->transport->fromTime)? $this->transport->fromTime : '00:00') .'-'.str_replace([':'], '', isset($this->transport->toTime)? $this->transport->toTime : '23:59') : '0000-2359',
                    'date_interval' => $this->transport->restrictions ? (isset($this->transport->fromDate) ? Carbon::parse($this->transport->fromDate)->format('Ymd') : Carbon::now()->format('Ymd')).'-'. (isset($this->transport->toDate) ? Carbon::parse($this->transport->toDate)->format('Ymd') : '21191231') : Carbon::now()->format('Ymd').'-21191231',
                ]
            ];

            info($data);
            // dd();

            $curl = curl_init();

            curl_setopt_array($curl, [
            //CURLOPT_PORT => "8082",
            CURLOPT_URL => $controller->ip. '/api/plate/srv',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
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
                if ($this->attempts() > 3) {
                    $users = \App\Models\User::all()->filter(function ($value, $key) {
                        return $value->isRoot();
                    });

                    foreach ($users as $key => $user) {
                        $user->notify(NovaNotification::make()
                            ->message('Ошибка доставки данных контроллеру '. $controller->name .'. Причина: '. $err)
                            ->type('error')
                        );
                    }
                    //$this->fail('Ошибка доставки данных контроллеру '. $controller->name .'. Причина: '. $err);
                } else {
                    $this->release(now()->addSeconds(1));
                }
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
                ->message($exception?->getMessage())
                ->type('error')
            );
        }
    }
}

<?php

namespace App\Nova\Actions;

use App\Models\Transport;
use App\Models\Controller;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Carbon\Carbon;

class SaveAllTransport extends Action
{
    use InteractsWithQueue, Queueable;

    public $name = 'Обновить транспорты на всех контроллерах';

    /**
     * Perform the action on the given models.
     *
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $controllers = Controller::all(['apikey', 'active', 'id', 'name', 'ip']);
        foreach ($controllers as $key => $controller) {
            if (! $controller->active) {
                continue;
            }
            $data = [
            'apikey' => $controller->apikey,
            'request_id' => Carbon::now()->format('Ymdhms'),
            'ev_date' => Carbon::now()->format('Y.m.d H:m:s'),
            ];
            foreach (Transport::all() as $key => $transport) {
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
                        'authentication' => $transport->type_auth ? $transport->type_auth : 'number',
                        'tid' => $transport->tid() ? (string) $transport->tid() : '0',
                    ],
                    'access' => [
                        'time_limit' => $transport->restrictions ? intval($transport->time_limit) : 0,
                        'week' => $transport->restrictions ? $week : '1111111',
                        'time_interval' => $transport->restrictions ? str_replace([':'], '', isset($transport->fromTime) ? $transport->fromTime : '00:00').'-'.str_replace([':'], '', isset($transport->toTime) ? $transport->toTime : '23:59') : '0000-2359',
                        'date_interval' => $transport->restrictions ? (isset($transport->fromDate) ? Carbon::parse($transport->fromDate)->format('Ymd') : Carbon::now()->format('Ymd')).'-'.(isset($transport->toDate) ? Carbon::parse($transport->toDate)->format('Ymd') : '21191231') : Carbon::now()->format('Ymd').'-21191231',
                    ],
                ];

            }

            $curl = curl_init();
            //info($controller->ip. '/api/plate/srv');
            curl_setopt_array($curl, [
                //CURLOPT_PORT => "8082",
                CURLOPT_URL => $controller->ip.'/api/plate/srv',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 100,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data), //http_build_query($data),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                info('cURL Error #: '.$err);
                $error = 'Ошибка доставки данных контроллеру '.$controller->name.'. Причина: '.$err;
            } else {
                //info($response);
            }

            sleep(3);
        }


        return isset($error) ? Action::danger($error) : Action::message('Обновления транспорта на контроллерах отправлены!');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [];
    }
}

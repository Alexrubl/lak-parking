<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\Events\JobFailed;
use App\Models\Tenant;
use App\Models\Transport;
use App\Models\History;
use App\Models\User;
use Laravel\Nova\Notifications\NovaNotification;


class CheckStatusCkassa implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Количество секунд ожидания перед повторной попыткой выполнения задания.
     *
     * @var int
     */
    public $backoff = 120;
    
    /**
     * Количество попыток выполнения задания.
     *
     * @var int
     */
    public $tries = 5;

    protected $user;

    /**
     * Создать новый экземпляр задания.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Выполнить задание.
     */
    public function handle(): void
    {
        info('job CheckStatusCkassa proccessing...');
        $curl = curl_init();

        $url = nova_get_setting('test_ckassa') ? 'https://demo.ckassa.ru/api-shop/rs/open' : 'https://api2.ckassa.ru/api-shop/rs/open';

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url. '/payments/new',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'ApiLoginAuthorization: '.(nova_get_setting('test_ckassa') ? nova_get_setting('test_ApiLoginAuthorization') : nova_get_setting('ApiLoginAuthorization')).'',
                'ApiAuthorization: '.(nova_get_setting('test_ckassa') ? nova_get_setting('test_ApiAuthorization') : nova_get_setting('ApiAuthorization')).'',
                'Content-Type: application/json'
            ),
        ));

        $response = json_decode(curl_exec($curl));
        $info = curl_getinfo($curl);
        curl_close($curl);
        info($url);
        info(nova_get_setting('test_ckassa'));
        info('payments status: ');
        info($response->payments);
        if (isset($response->payments) && !empty($response->payments)) {
            $payedCount = 0;
            foreach ($response->payments as $key => $value) {
                if ($value->state == 'PAYED') {
                    $payedCount++;

                    $tenant = Tenant::find(intval($value->tgInvPayer));
                    $tenant->balance = $tenant->balance + (intval($value->amount) / 100);                    
                    $tenant->save();

                    foreach (Transport::where('tenant_id', $tenant->id) as $key => $transport) {
                        $transport->access = 1;
                        $transport->save();
                    }

                    $history = new History;
                    $history->tenant_id = $tenant->id;
                    $history->price = intval($value->amount) / 100;
                    $history->comment = 'Пополнение';
                    $history->save();

                    $this->user->notify(NovaNotification::make()
                        ->message('Оплачен счёт '. $value->regPayNum .' на сумму '. (intval($value->amount) / 100) .' р.')
                        ->type('info')
                    );

                    info('Оплачен счёт: '. $value->regPayNum); 
                }
            }
            if ($payedCount < 1) {
                throw new \App\Exceptions\PaymentsNotFoundException('Нет ничего в ответе.');
            }            
        } else {
            throw new \App\Exceptions\PaymentsNotFoundException('Нет ничего в ответе.');         
        }
    }

    /**
     * Обработать провал задания.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        // Отправляем пользователю уведомление об ошибке и т.д.
        info('job CheckStatusCkassa failed');
    }
}

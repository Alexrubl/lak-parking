<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use App\Jobs\CheckStatusCkassa;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Transport;
use App\Models\History;

class CkassaController extends Controller
{
    public function show(): View
    {
        return view('ckassa', []);
    }

    public static function invoice($models, $fields, User $user)
    {
        // $vars = [ 
            //     "servCode" => "111-18298-1",
            //     "tgInvPayer" => "444-123456789012",
            //     "startPaySelect" => "false",
            //     "invType" => "READ_ONLY",
            //     "amount" => "1000",
            //     "bestBefore" => "17-12-2023 19:50:07 +0500",
            //     "nodeName" => "ACQ4I",
            //     "startPaySelect" => true,
            //     "properties" => [
            //         "Л/СЧЕТ",
            //         "12345678"
            //     ]
            // ];
            // $url = "https://demo.ckassa.ru/api-shop/rs/open/invoice/create2";
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_HEADER, [
            //     'accept: text/plain',
            //     'ApiLoginAuthorization: Tech_LacquerCoating',
            //     'ApiAuthorization: SVO7RIR0H-HAOO-NPJ-5E4XL-FGXY6SBYLDN',
            //     'Content-Type: application/json'
            // ]);
            // curl_setopt($ch, CURLOPT_POST, 1);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            // $head = curl_exec($ch);
            // $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            // curl_close($ch);
            // dd($head) ;

            //return view('ckassa', []);

            // info('{
            //         "servCode": "'.(nova_get_setting('test_ckassa') ? nova_get_setting('test_servCode') : nova_get_setting('servCode')).'",
            //         "amount": "'.($fields->sum * 100).'",
            //         "tgInvPayer": "'.$models[0]->id.'", 
            //         "startPaySelect": "true",
            //         "bestBefore": "'. Carbon::now()->addminutes(10)->format('d-m-Y H:i:s O') . '",
            //         "invType": "READ_ONLY",
            //         "properties": [
            //                 "Технопарк",   
            //                 "22222"
            //         ]
            //     }');
            //  info(
            //     array(
            //         'ApiLoginAuthorization: '.(nova_get_setting('test_ckassa') ? nova_get_setting('test_ApiLoginAuthorization') : nova_get_setting('ApiLoginAuthorization')).'',
            //         'ApiAuthorization: '.(nova_get_setting('test_ckassa') ? nova_get_setting('test_ApiAuthorization') : nova_get_setting('ApiAuthorization')).'',
            //         'Content-Type: application/json'
            //     ),
            //  );

            // $curl = curl_init();

                // $url = nova_get_setting('test_ckassa') ? 'https://demo.ckassa.ru/api-shop/rs/open' : 'https://api2.ckassa.ru/api-shop/rs/open';
                // info(nova_get_setting('test_ckassa'));
                // info($url);

                // curl_setopt_array($curl, array(
                //     CURLOPT_URL => $url. '/invoice/create2',
                //     CURLOPT_RETURNTRANSFER => true,
                //     CURLOPT_ENCODING => '',
                //     CURLOPT_MAXREDIRS => 10,
                //     CURLOPT_TIMEOUT => 10,
                //     CURLOPT_FOLLOWLOCATION => true,
                //     CURLOPT_SSL_VERIFYPEER => false,
                //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //     CURLOPT_CUSTOMREQUEST => 'POST',
                //     CURLOPT_POSTFIELDS =>'{
                //         "servCode": "'.(nova_get_setting('test_ckassa') ? nova_get_setting('test_servCode') : nova_get_setting('servCode')).'",
                //         "amount": "'.($fields->sum * 100).'",
                //         "tgInvPayer": "'.$models[0]->id.'", 
                //         "startPaySelect": true,
                //         "bestBefore": "'. Carbon::now()->addminutes(10)->format('d-m-Y H:i:s O') . '",
                //         "invType": "READ_ONLY",
                //         "properties": [
                //                 "'.$models[0]->name.'",
                //                 "'.$models[0]->id.'"
                //         ]
                //     }',
                //     CURLOPT_HTTPHEADER => array(
                //         'ApiLoginAuthorization: '.(nova_get_setting('test_ckassa') ? nova_get_setting('test_ApiLoginAuthorization') : nova_get_setting('ApiLoginAuthorization')).'',
                //         'ApiAuthorization: '.(nova_get_setting('test_ckassa') ? nova_get_setting('test_ApiAuthorization') : nova_get_setting('ApiAuthorization')).'',
                //         'Content-Type: application/json'
                //     ),
                // ));

                // $response = curl_exec($curl);
                // $info = curl_getinfo($curl);
                // curl_close($curl);
                // CheckStatusCkassa::dispatch($user);
                // return $response;

            // ShopToken: 3ccfa0a3-1ee1-470e-89e9-1930fe58d1d3
            // secKey: fefb5624-3d3c-4894-a070-33bfcb91811a
            // servCode: 111-18298-1
            // callbackUrl: https://auto.npolkp.ru/api/ckassa/callback

        $curl = curl_init();

        $url = nova_get_setting('test_ckassa') ? 'https://demo-api2.ckassa.ru/api-shop/do/payment/anonymous' : 'https://api2.ckassa.ru/api-shop/do/payment/anonymous';

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "serviceCode": "'.(nova_get_setting('test_ckassa') ? nova_get_setting('test_servCode') : nova_get_setting('servCode')).'",
                "amount": "'.($fields->sum * 100).'",
                "comission": "0",
                "pfPayLink": true,
                "properties": [
                    {
                        "name": "НАЗВАНИЕ_ОРГ",
                        "value": "'.$models[0]->name.'"
                    },
                    {
                        "name": "ИДЕНТИФИКАТОР",
                        "value": "'.$models[0]->id.'"
                    }
            ]
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic '.base64_encode(nova_get_setting('test_ckassa') ? nova_get_setting('test_ShopToken').':'.nova_get_setting('test_secKey') : nova_get_setting('ShopToken').':'.nova_get_setting('secKey')).'', 
            ),
        ));
        info(
            array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                "serviceCode": "'.(nova_get_setting('test_ckassa') ? nova_get_setting('test_servCode') : nova_get_setting('servCode')).'",
                "amount": '.($fields->sum * 100).',
                "comission": 0,
                "pfPayLink": true,
                "properties": [
                    {
                        "name": "НАЗВАНИЕ_ОРГ",
                        "value": "'.nova_get_setting('test_organization').'"
                    },
                    {
                        "name": "ИДЕНТИФИКАТОР",
                        "value": "'.nova_get_setting('test_identificator').'"
                    }
            ]
            }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic '.base64_encode(nova_get_setting('test_ckassa') ? nova_get_setting('test_ShopToken').':'.nova_get_setting('test_secKey') : nova_get_setting('ShopToken').':'.nova_get_setting('secKey')).'', 
            ),
        )
            
        );

        $response = curl_exec($curl);

        curl_close($curl);
       // info('invoice ответ ');
       // info(json_decode($response));
        return $response;
    }

    public static function status() 
    {        
        $curl = curl_init();

        $url = nova_get_setting('test_ckassa') ? 'https://demo.ckassa.ru/api-shop/rs/open' : 'https://api2.ckassa.ru/api-shop/rs/open';
        info($url);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url.'/payments/new',
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

        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);
        info('Pay Status:');
        info($response);
        return $response;
    }

    public function callback(Request $request) {
        info('==========callback==========');
        info($request->map['НАЗВАНИЕ_ОРГ']);
        // (
        //     'regPayNum' => '131016599825',
        //     'amount' => 10000,
        //     'state' => 'payed',
        //     'errorCode' => 0,
        //     'errorMsg' => NULL,
        //     'map' =>
        //     array (
        //     'НАЗВАНИЕ_ОРГ' => 'Бизон',
        //     'ИДЕНТИФИКАТОР' => '1003',
        //     ),
        //     'transactionId' => 'MN22_jFZf9l2DYONU1ZZA41LWs1TfzDDJ1DtCjCXgsiEvC9IhFzJYGpOgh4ZpiLkKMW-i7Gg30IsNm9FBdhEnw==',
        // )

        if ($request->state == 'payed') {

            $tenant = Tenant::find(intval($request->map['ИДЕНТИФИКАТОР']));
            $tenant->balance = $tenant->balance + (intval($request->amount) / 100);                    
            $tenant->save();

            foreach (Transport::where('tenant_id', $tenant->id)->get() as $key => $transport) {
                $transport->access = 1;
                $transport->save();
            }

            $history = new History;
            $history->tenant_id = $tenant->id;
            $history->price = intval($request->amount) / 100;
            $history->comment = 'Пополнение';
            $history->save();
            logist('Пополнен баланс '.$tenant->name. ' на сумму '.(intval($request->amount) / 100). ' руб.');

            foreach ($tenant->contacts as $key => $user) {
                info(collect($tenant->contacts));
                Notification::send(
                    $user,
                    NovaNotification::make()
                        ->message('Оплачен счёт '. $request->regPayNum .' на сумму '. (intval($request->amount) / 100) .' р.')
                        ->type('info')
                    
                );
                if ($user->email) {
                    $data['text'] = 'Оплачен счёт '. $request->regPayNum .' на сумму '. (intval($request->amount) / 100) .' р.';
                    $data['email'] = $user->email;
                    dispatch(new \App\Jobs\sendMail($data));
                }
            }  
        }
        return response()->json(['message' => 'success'], 200);        
    }
}

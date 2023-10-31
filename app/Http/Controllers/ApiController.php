<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transport;
use App\Models\History;
use App\Models\Tenant;
use App\Models\Rate;
use App\Models\Controller;
use Carbon\Carbon;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class ApiController extends Controller
{
    public function event(Request $request) {
        //info($request);
        //$data = $request;
        $fail = collect([
            'apikey' => $request->apikey,
            'request_id' => $request->request_id,
            'status' => 'ok'
        ]);
        $controller = Controller::where('apikey', $request->apikey)->first();
        if (!isset($controller)) {
            return response()->json($fail->put('message', 'Неизвестный apikey.'), 401);
        }
        $transport = Transport::where('number', $request->plate)->first();
        if (!isset($transport)) {
            return response()->json($fail->put('message', 'Не найден транспорт с таким номером.'), 200);
        }
        $tenant = $transport->tenant;
        $rate = $transport->rate;
        if ($request->access == 'enable') {
            $tenant->balance = $tenant->balance - $rate->getPrice($transport);            
            $tenant->save();
            $history = new History;
            $history->tenant_id = $tenant->id;
            $history->transport_id = $transport->id;
            $history->comment = 'Списание';
            $history->price = $rate->getPrice($transport);
            $history->save();
            
            return response()->json([
                    'apikey' => $request->apikey,
                    'request_id' => $request->request_id,
                    'status' => 'ok',
                    'message' => 'Успешно.'
                ], 200);
        } 
        return response()->json($fail->put('message', 'неизвестная ошибка.'), 200);
    }
 
    
    // public static function sendNewTransportToControllers(Request $request) {  
    //     $controllers = Controller::all();
    //     foreach ($controllers as $key => $controller) {
    //         $week = '';
    //         foreach (json_decode($request->week) as $key => $value) {
    //             $week .= ($value == 1) ? '1':'0';
    //         }
    //         //info($week);
    //         $dateInterval = explode(' - ', $request->{'fromDate-toDate'});
    //         $timeInterval = explode(',', $request->{'fromTime-toTime'});
    //         $data = [
    //             'apikey' => $controller->apikey,
    //             'request_id' => Carbon::now()->format('Ymdhms'),
    //             'ev_date' => Carbon::now()->format('Y.m.d H:m:s'),
    //             'create' => [ 
    //                 'plate' => $request->number,
    //                 'fio' => $request->driver,
    //                 'access' => intval($request->access),
    //             ],
    //             'access' => [
    //                 'time_limit' => intval($request->time_limit),
    //                 'week' => $week,
    //                 'time_interval' => str_replace([':'], '', $timeInterval[0]) .'-'.str_replace([':'], '', $timeInterval[1]),
    //                 'date_interval' => Carbon::parse($dateInterval[0])->format('Ymd').'-'.Carbon::parse($dateInterval[1])->format('Ymd'),
    //             ]
    //         ];
    //         // info('data:');
    //         // info(json_encode($data));

    //         $curl = curl_init();

    //         curl_setopt_array($curl, [
    //         //CURLOPT_PORT => "8082",
    //         CURLOPT_URL => $controller->ip. '/api/plate/srv',
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => "",
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 30,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => "POST",
    //         CURLOPT_POSTFIELDS => json_encode($data), //http_build_query($data),
    //         CURLOPT_HTTPHEADER => [
    //             "Content-Type: application/json"
    //         ],
    //         ]);

    //         $response = curl_exec($curl);
    //         $err = curl_error($curl);

    //         curl_close($curl);

    //         if ($err) {
    //             info("cURL Error #: " . $err);
    //         } else {
    //             //info($response);
    //         }
    //     }
    //     //info(json_encode($data));       

    // }

        public static function sendNewTransportToControllers($transport) {  
        $controllers = Controller::all();
        foreach ($controllers as $key => $controller) {
            $week = '';
            foreach ($transport->week as $key => $value) {
                $week .= ($value == 1) ? '1':'0';
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
                ],
                'access' => [
                    'time_limit' => intval($transport->time_limit),
                    'week' => $week,
                    'time_interval' => str_replace([':'], '', $transport->fromTime) .'-'.str_replace([':'], '', $transport->toTime),
                    'date_interval' => (isset($transport->fromDate) ? Carbon::parse($transport->fromDate)->format('Ymd') : Carbon::now()->format('Ymd')).'-'. (isset($transport->toDate) ? Carbon::parse($transport->toDate)->format('Ymd') : '21191231'),
                ]
            ];

            $curl = curl_init();

            curl_setopt_array($curl, [
            //CURLOPT_PORT => "8082",
            CURLOPT_URL => $controller->ip. '/api/plate/srv',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
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
            } else {
                //info($response);
            }
        }
        //info(json_encode($data));
    }

    public function test_createTransport(Request $request) {        
        $transport = Transport::where('number', 'A456OA25')->first();
        if (!isset($transport)) {
            return response()->json(['message' => 'Не найдено авто'], 200);
        }
        $week = '';
        foreach ($transport->week as $key => $value) {
            $week .= ($value == 1) ? '1':'0';
        }
        $data = [
            'apikey' => Controller::first()->apikey,
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
                'access' => $transport->access == 'enable' ? 1: 0,
            ],
            'access' => [
                'time_limit' => $transport->time_limit,
                'week' => $week,
                'time_interval' => str_replace([':'], '', $transport->fromTime) .'-'.str_replace([':'], '', $transport->toTime),
                'date_interval' => Carbon::parse($transport->fromDate)->format('Ymd').'-'.Carbon::parse($transport->toDate)->format('Ymd'),
            ]
        ];

        return response()->json($data, 200);
    }

    public function ffmpeg() {
        info('ffmpeg');
        // FFMpeg::openUrl('rtsp://test:123456789qQ@5.165.25.145:55554')
        // ->export()
        // ->inFormat(new X264)
        // ->concatWithTranscoding($hasVideo = true, $hasAudio = true)
        // ->save('concat.mp4');
        // FFMpeg::openUrl('rtsp://test:123456789qQ@5.165.25.145:55554')
        //     ->export()
        //     ->onProgress(function ($percentage) {
        //         echo "{$percentage}% transcoded";
        //     });

        $lowBitrate = (new \FFMpeg\Format\Video\X264)->setKiloBitrate(250);
        $midBitrate = (new \FFMpeg\Format\Video\X264)->setKiloBitrate(500);
        $highBitrate = (new \FFMpeg\Format\Video\X264)->setKiloBitrate(1000);

        FFMpeg::$transport->fromDate
        ->openUrl('rtsp://test:123456789qQ@5.165.25.145:55554')
        ->exportForHLS()
        ->toDisk('public')
        ->setSegmentLength(10) // optional
        ->setKeyFrameInterval(48) // optional
        ->addFormat($lowBitrate)
        ->addFormat($midBitrate)
        ->addFormat($highBitrate)
        ->save('adaptive_steve.m3u8');

        return 'ok';
        
    }
}

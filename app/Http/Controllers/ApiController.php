<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transport;
use App\Models\TypeTransport;
use App\Models\History;
use App\Models\Tenant;
use App\Models\Rate;
use App\Models\User;
use App\Models\Sigur;
use App\Models\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Laravel\Nova\Notifications\NovaNotification;
use Illuminate\Support\Facades\Notification;
use Storage;
use Response;

class ApiController extends Controller
{
    public function event(Request $request) {
        //info('event from controller:');
        //info($request);
        $image_name = null;
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
    
        if (isset($request->UrlPhoto)) {
            try {
                $image_name = $this->setImage($controller->ip. '/assets/img/'. $request->UrlPhoto, $controller->id.'/'.Carbon::now()->format('Ymd'));
            } catch (\Throwable $th) {
                info($th->getMessage());
            }            
        }
        
        // Для сигура
        $sigur = new Sigur;
        $sigur->controller_id = $controller->id;
        $sigur->number = $request->plate;
        $sigur->direction = $request->entry == 'in' ? 'up' : 'down';
        $sigur->save();
        // Конец блока для сигура  

        $transport = Transport::where('number', $request->plate)->first();
        if (!isset($transport) || !isset($transport->tenant)) {
            logist(($request->entry && $request->entry == "in" ? 'Запрос на въезд. ':'Запрос на выезд. ').'Номер транспорта: '.$request->plate.', доступ ЗАПРЕЩЁН (не найден транспорт с таким номером)', $image_name, Controller::where('apikey', $request->apikey)->first()->id, $request->entry);
            return response()->json($fail->put('message', 'Не найден транспорт с таким номером или некорректно заполнены данные.'), 200);
        }            

        $tenant = $transport->tenant;
        $rate = $transport->rate;        
        if ($request->access == 'enable' || $request->access == 1) {
            $sum = 0;
            if ($request->entry == 'in' || $request->entry == 1) {
                if ($transport->inside <> 1) { 
                    $transport->inside = 1;
                    $sum = $rate->getPrice($transport);
                    $transport->save();
                }
            } else {
                $transport->inside = 0;
                if ($transport->guest == true) {
                    $transport->access= 0;                    
                }  
                $transport->save();             
            } 
            $tenant->balance -= $sum;            
            $tenant->save();
            $history = new History;
            $history->controller_id = $controller->id;
            $history->tenant_id = $tenant->id;
            $history->transport_id = $transport->id;
            $history->comment = ', '. $request->entry ? 'Списание, Въезд' : 'Выезд';
            $history->direction = $request->entry;
            $history->price = $sum;
            $history->image = $image_name;
            $history->created_at = Carbon::createFromFormat('Y-m-d H:i:s', $request->ev_date);
            $history->save();

            if ($tenant->balance < 1) {
                foreach (User::all() as $key => $user) {
                    foreach ($user->tenant as $t) {
                        if ($t->id == $tenant->id) {
                            Notification::send(
                                $user,
                                NovaNotification::make()
                                    ->message('У арендатора '. $tenant->name .' отрицательный баланс!')
                                    ->type('info')
                                
                            );
                            if ($user->email) {
                                $data['text'] = 'Уведомляем Вас, что у арендатора '. $tenant->name .' отрицательный баланс!';
                                $data['email'] = $user->email;
                                dispatch(new \App\Jobs\sendMail($data));
                            }
                        }
                    }                    
                }
                foreach ($tenant->transport as $key => $transp) {
                    if ($transp->inside == 0) {
                        $transp->access = 0;
                        $transp->save();
                    }
                }
            }
            if (nova_get_setting('openForceEntry')) {
                $this->openGate($request);
                if ($controller->auto_close) {
                    dispatch(new \App\Jobs\CloseEntry($controller));
                }
            }
            logist(($request->entry && $request->entry == "in" ? 'Запрос на въезд. ':'Запрос на выезд. ').'Номер транспорта: '.$request->plate.', доступ '.($request->access = 1 ? 'РАЗРЕШЁН':'ЗАПРЕЩЁН').($sum > 0 ? ', Списано '.$sum.' руб.' :''), $image_name, Controller::where('apikey', $request->apikey)->first()->id, $request->entry);
            return response()->json([
                    'apikey' => $request->apikey,
                    'request_id' => $request->request_id,
                    'status' => 'ok',
                    'message' => 'Успешно.'
                ], 200);
        } 

        logist(($request->entry && $request->entry == "in" ? 'Запрос на въезд. ':'Запрос на выезд. ').'Номер транспорта: '.$request->plate.', доступ ЗАПРЕЩЁН', $image_name, Controller::where('apikey', $request->apikey)->first()->id, $request->entry);
        return response()->json($fail->put('message', 'неизвестная ошибка.'), 200);
    }

    # Фиксация проезда, списывыние баланса
    # transport - Указываем транспорт
    #
    function fixEntry(Controller $controller, Transport $transport, Tenant $tenant = null) {
        if (!isset($transport)) {
            return 1;
        }
        $tenant = $transport->tenant;
        $rate = $transport->rate;
        $sum = 0;
        if ($transport->inside <> 1) { # Если транспорт не внутри
            $transport->inside = 1;
            $sum = $rate->getPrice($transport);
            $transport->save();
        } else {
            $transport->inside = 0;
            if ($transport->guest == true) {
                $transport->access= 0;                    
            }  
            $transport->save();             
        } 
        $tenant->balance -= $sum;            
        $tenant->save();
        $history = new History;
        $history->controller_id = $controller->id;
        $history->tenant_id = $tenant->id;
        $history->transport_id = $transport->id;
        $history->comment = 'Списание, Въезд c кнопки охраны';
        $history->direction = 'in';
        $history->price = $sum;
        $history->image = null;        
        $history->save();

        if ($tenant->balance < 1) {
            foreach (User::all() as $key => $user) {
                foreach ($user->tenant as $t) {
                    if ($t->id == $tenant->id) {
                        Notification::send(
                            $user,
                            NovaNotification::make()
                                ->message('У арендатора '. $tenant->name .' отрицательный баланс!')
                                ->type('info')
                            
                        );
                        if ($user->email) {
                            $data['text'] = 'Уведомляем Вас, что у арендатора '. $tenant->name .' отрицательный баланс!';
                            $data['email'] = $user->email;
                            dispatch(new \App\Jobs\sendMail($data));
                        }
                    }
                }                    
            }
            foreach ($tenant->transport as $key => $transp) {
                if ($transp->inside == 0) {
                    $transp->access = 0;
                    $transp->save();
                }
            }
        }
        logist('Запрос на въезд. Номер транспорта: '.$transport->number.', доступ c кнопки охраны. '. ($sum > 0 ? ', Списано '.$sum.' руб.' :''), null, $controller->id, 'in');
        return 0;
    } 
    
    public static function sendNewTransportToControllers($transport) {  
        $controllers = Controller::all();
        foreach ($controllers as $key => $controller) {
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

    function openGate(Request $request) {
        if( $request->has('controller_id')) {
            $controller = Controller::find($request->controller_id);
            info(collect($controller));
        }
        if ($request->has('apikey')) {
            $controller = Controller::where('apikey', $request->apikey)->first();
            info(collect($controller));
        }
        if (!isset($controller)) {
            return response()->json(['status' => false,'message' => 'Не найден контроллер'], 200);
        }

        if ($request->has('transport_id')) {
            $transport = Transport::find($request->transport_id)->first();
            $tenant = $transport->tenant;
            $this->fixEntry($controller, $transport);
        }       

        return response()->json(['status' => false,'message' => 'Не найден контроллер'], 200); 

        $curl = curl_init();

        if ($controller->method == 'url') {
            $url = $controller->url_open;
            $CURLOPT_CUSTOMREQUEST = 'GET';
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            $header = [
                    "Content-Type: application/x-www-form-urlencoded"
                ];
        } else {
            if(isset($controller->id_open_stream)) {
                return response()->json(['status' => false,'message' => 'Не заполнен ID Stream'], 200);
            } 
            $url = $controller->ip.'/api/plate/serv';
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            $data = [
                'apikey' => Controller::first()->apikey,
                'request_id' => Carbon::now()->format('Ymdhms'),
                'stream_uuid' => $controller->id_open_stream,
                'p_open' => 1
            ];
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $header = [
                    "Content-Type: application/json"
                ];
        }

        curl_setopt_array($curl, [
            //CURLOPT_PORT => "8082",
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => $header
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            info("cURL Error #: " . $err.'. Code http status: '.$httpcode);
            return response()->json(['message' => 'cURL Error #: '.$err, 'status' => $httpcode], 503); 
        } else {
            info('openGate (Code http status: '.$httpcode.'):');
            info($response);
        }
        logist('Открытие проезда с кнопки охраны.');
        return response()->json($response, 200);        
    }

    function closeGate(Request $request = null, $controller = null) {
        if( isset($request) && $request->has('controller_id')) {
            $controller = Controller::find($request->controller_id);
            info(collect($controller));
        }
        if (isset($request) && $request->has('apikey')) {
            $controller = Controller::where('apikey', $request->apikey)->first();
            info(collect($controller));
        }
        if (!isset($controller)) {
            return response()->json(['status' => false,'message' => 'Не найден контроллер'], 200);
        }

        $curl = curl_init();

        if ($controller->method == 'url') {
            $url = $controller->url_close;
            $CURLOPT_CUSTOMREQUEST = 'GET';
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
            $header = [
                    "Content-Type: application/x-www-form-urlencoded"
                ];
        } else {
            if(isset($controller->id_open_stream)) {
                return response()->json(['status' => false,'message' => 'Не заполнен ID Stream'], 200);
            } 
            $url = $controller->ip.'/api/plate/serv';
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            $data = [
                'apikey' => Controller::first()->apikey,
                'request_id' => Carbon::now()->format('Ymdhms'),
                'stream_uuid' => $controller->id_open_stream,
                'p_open' => 1
            ];
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $header = [
                    "Content-Type: application/json"
                ];
        }

        curl_setopt_array($curl, [
            //CURLOPT_PORT => "8082",
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPHEADER => $header
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($err) {
            info("cURL Error #: " . $err.'. Code http status: '.$httpcode);
            return response()->json(['message' => 'cURL Error #: '.$err, 'status' => $httpcode], 503); 
        } else {
            info('closeGate (Code http status: '.$httpcode.'):');
            info($response);
        }
        logist('Закрытие проезда с кнопки охраны.');
        return response()->json($response, 200);        
    }

    public function getLogs(Request $request) {
        // info($request);
        if ($request->entry == 'in') {
            return response()->json(\App\Models\Log::where('controller_id', $request->controller_id)                                                    
                                                    ->where('entry', $request->entry)->orWhereNull('entry')
                                                    ->latest('id')
                                                    ->take(25)
                                                    ->get());
        } else {
            return response()->json(\App\Models\Log::where('controller_id', $request->controller_id)
                                                    ->where('entry', $request->entry)
                                                    ->latest('id')
                                                    ->take(25)
                                                    ->get());
        }
    }

    private function http_check($url) {
        $return = $url;
        if ((!(substr($url, 0, 7) == 'http://')) && (!(substr($url, 0, 8) == 'https://'))) {
            $return = 'http://' . $url;
        }
        return $return;
    } 

    public function setImage($value, $path = 'images') {
        $attribute_name = "image";
        $disk = "public";
        $destination_path = $path;

        $this->http_check($value);

        info($value);

        if (isset($value))
        {
            $image = \Image::make($value);
            if ($image->width() > 1080) {
                $image->resize(1080, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
            $filename = $attribute_name.Carbon::now()->format('YmdHis').'.jpg';
            // 2. Store the image on disk.
            \Storage::disk($disk)->put($destination_path.'/'.$filename, $image->stream('jpg', 85));
            // 3. Save the path to the database
            return $destination_path . '/' . $filename;
        }
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


    public function test_234(Request $request) {
        //return response()->json([], 200);
        $path = 'http://89.109.239.73:26084/ISAPI/Streaming/channels/101/picture';
        // $type = pathinfo($path, PATHINFO_EXTENSION);
        // info($type);
        $auth = base64_encode("user:password12345678");
        $context = stream_context_create([
            "http" => [
                "header" => "Authorization: Basic $auth"
            ]
        ]);
        $data = file_get_contents($path, true, $context);
        info($data);
        // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        // file_put_contents("file.png", file_get_contents($base64));

        $curl = curl_init();

        curl_setopt_array($curl, [
        //CURLOPT_PORT => "8082",
        CURLOPT_URL => 'http://89.109.239.73:26084/ISAPI/Streaming/channels/101/picture',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        // CURLOPT_POSTFIELDS => json_encode($data), //http_build_query($data),
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

    public function testSendEmail() {
        $data['text'] = 'Какой то текст';
        $data['email'] = 'alexrubl@mail.ru';
        dispatch(new \App\Jobs\sendMail($data));
        return response()->json($data, 200);
    }

    public function testSigurEvent(Request $request) {
        info($request);
        $data = [
            "type" => "0ab0a061-12ec-4092-831d-33afe4f8a5f7"
        ];
        return response()->json($data, 200);
    }

    public function searchTransport(Request $request, $searchText) {
        $transports = Transport::where('number', 'like', '%'.$searchText.'%' )->get();
        foreach ($transports as $key => $value) {
            $data['transports'][] = [
                'value' => $value->id,
                'text' => $value->number. ' - ' .$value->name,
                'number' => $value->number,
                'tenant_id' => $value->tenant_id
            ];
            $data['tenants'][] = [
                'id' => $value->tenant->id,
                'name' => $value->tenant->name  
            ];
        }
        return response()->json($data, 200);
    }

    public function searchTenant(Request $request, $searchText) {
        $data = [
            'tenants' => [],
            'transports' => []
        ];
        $tenants = Tenant::where('name', 'like', '%'.$searchText.'%' )->get();
        foreach ($tenants as $key => $value) {
            $data['tenants'][] = [
                'id' => $value->id,
                'name' => $value->number .' '.$value->name
            ];
            $transports = Transport::where('tenant_id', $value->id)->get();
            foreach ($transports as $key => $value) {
                 $data['transports'][] = [
                    'value' => $value->id,
                    'text' => $value->number.' - '.$value->name,
                    'number' => $value->number,
                    'tenant_id' => $value->tenant_id
                ];
            }
        }
        return response()->json($data, 200);
    }

    public function getTypeTransport() {
        return response()->json(TypeTransport::all('id', 'name'), 200);
    }

    function createPass(Request $request) {
        info($request);
        $validated = $request->validate([
            'name' => 'required|max:255',
            'number' => 'required|unique:transports',
            'tenant_id' => 'required',
            'type_id' => 'required',
        ]);
        $transport = new Transport;
        $transport->fill($request->all());
        $transport->rate_id = Rate::where('default_guest', 1)->first()->id;
        $transport->save();

        return response()->noContent();
    }

    public function sigurEventNumber(Request $request) {
        //info('Сигур событие, ответ: ');
        // $history = History::where('skud_send', false)->orWhereNull('skud_send')->first();
        // //foreach ($history as $value) {
        // if (isset($history)){
        //     $transport = Transport::find($history->transport_id);
        //     $controller = Controller::find($history->controller_id);
        //     if (isset($controller) && isset($transport) && isset($history->direction)) {                
        //         $data = [
        //             "type" => "9183e0da-8ab7-4d86-a6d7-5745cb514032",
        //             "channelId" => (string) $controller->id,
        //             "number" => $transport->number,
        //             "direction" => $history->direction == 'in' ? 'up' : 'down'
        //         ];
        //     }
        //     $history->skud_send = true;
        //     $history->save();
        // }
        //}
        // dd($data);
        // {
        //     "type": "9183e0da-8ab7-4d86-a6d7-5745cb514032",
        //     "channelId": "0ecc767e-e36b-4486-ad9d-13cad1f256e6",
        //     "number": "A123AA12",
        //     "direction": "down"
        // }
        $sigur = Sigur::first();
        if (isset($sigur)){              
            $data = [
                "type" => "9183e0da-8ab7-4d86-a6d7-5745cb514032",
                "channelId" => (string) $sigur->controller_id,
                "number" => $sigur->number,
                "direction" => $sigur->direction
            ];
            $sigur->delete();
        }
        if (!isset($data)) {
            $data = [
                "type" => "0ab0a061-12ec-4092-831d-33afe4f8a5f7"
            ];
        }
        //info('Ответ:');
        //info(json_encode($data));
        return response()->json($data, 200);
    }

    public function sigurGetChannels(Request $request) {
        info('GetChannels');
        $controllers = Controller::all();
        foreach ($controllers as $key => $value) {
            $data['channels'][] = ['id' => (string) $value->id, 'name' => $value->name];
        }
        return response()->json($data, 200);
    }
}

<?php

use App\Models\Log;


if (!function_exists('logist')) {
    function logist($text, $controller_id = null, $entry = null) {
       $log = new Log;
       $log->controller_id = $controller_id;
       $log->entry = $entry;
       $log->text = $text;
       $log->save(); 
       \Log::channel('entry')->info($text);    
    }
}




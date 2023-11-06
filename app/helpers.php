<?php

use App\Models\Log;


if (!function_exists('logist')) {
    function logist($text) {
       $log = new Log;
       $log->text = $text;
       $log->save(); 
       \Log::channel('entry')->info($text);    
    }
}




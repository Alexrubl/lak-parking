<?php

use App\Models\Log;
use App\Events\Notify;

if (! function_exists('logist')) {
    function logist($text, $image = null, $controller_id = null, $entry = null)
    {
        $log = new Log;
        $log->controller_id = $controller_id;
        $log->entry = $entry;
        $log->text = $text;
        $log->image = $image;
        $log->save();
        \Log::channel('entry')->info($text);
        if (isset($entry)) {
            Notify::dispatch([
                "message" => $text,
                "type" => str_contains($text, 'РАЗРЕШЁН') ? 'success' : 'danger'
            ]);
        }
    }
}

if (! function_exists('info_d')) {
    function info_d($val)
    {
        if (config('app.debug') == true) {
            info($val);
        }
    }
}

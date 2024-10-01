<?php

namespace Maatwebsite\LaravelNovaExcel\Requests;

use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\LensActionRequest;

class ExportActionRequestFactory
{
    public static function make(ActionRequest $request): ExportActionRequest
    {
        if ($request instanceof LensActionRequest) {
            return ExportLensActionRequest::createFrom($request);
        }

        return ExportResourceActionRequest::createFrom($request);
    }
}

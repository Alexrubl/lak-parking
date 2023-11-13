<?php

namespace App\Nova\Repeater;

use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Heading;
use NormanHuth\NovaRadioField\Radio;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;

class Camera extends Repeatable
{
    public static $title = 'name';

    public static function label() {
        return 'Камеры';
    }

    public static function singularlabel() {
        return 'Камера';
    }

    /**
     * Get the fields displayed by the repeatable.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Text::make('Наименование', 'name')
                ->fullWidth(),
            Text::make('RTSP Stream', 'url_rtsp')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('ссылка на поток с камеры. Например rtsp:/10.10.10.10/live/stream1')
                ->fullWidth(),
            Text::make('URL Stream', 'url')
                ->sortable()
                ->rules('required', 'max:255')
                ->help('ссылка на поток с камеры в формате hls. Например /stream/camera1/file.m3u8')
                ->fullWidth(),
            Select::make('Смотрит', 'entry')
                ->options([
                    'in' => 'in',
                    'out' => 'out'
                ])->rules('required')           
        ];
    }
}

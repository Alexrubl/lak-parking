<?php

namespace App\Nova\Repeater;

use Laravel\Nova\Fields\Repeater\Repeatable;
use Laravel\Nova\Fields\Text;
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
            Text::make('Наименование', 'name'),
            Text::make('Url', 'url')
                ->sortable()
                ->rules('required', 'max:255'),
        ];
    }
}

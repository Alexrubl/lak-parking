<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Repeater;
use Laravel\Nova\Fields\Heading;
use NormanHuth\NovaRadioField\Radio;
use Formfeed\DependablePanel\DependablePanel;
use Laravel\Nova\Fields\FormData;

class Controller extends Resource
{
    public static $group = '  Справочники';

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Controller>
     */
    public static $model = \App\Models\Controller::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static function label() {
        return 'Контроллеры';
    }

    public static function singularlabel() {
        return 'Контроллер';
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name'
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Boolean::make('Вкл.', 'active')->default(false),
            
            ID::make()->sortable(),
            
            Text::make('Наименование', 'name')
                ->sortable()
                ->rules('required', 'max:255'),
            
            Text::make('IP:порт', 'ip')
                ->sortable()
                ->rules('required', 'max:255'),
            
            Text::make('apikey', 'apikey')
                ->sortable()
                ->rules('required', 'max:255'),          
                
            Heading::make('Камеры'),           

            Repeater::make('', 'cameras')
                ->repeatables([
                    \App\Nova\Repeater\Camera::make(),
                ])
                ->asJson()->fullWidth(),

            Heading::make('Метод открытия'),
            Radio::make('Открыть по', 'method')
                ->options([
                    'id' => 'id',
                    'url' => 'url',
                ])->inline()->rules('required')->hideFromIndex(),
            Text::make('ID потока', 'id_open_stream')
                ->sortable()
                ->rules('max:255')->hideFromIndex(), 
            Text::make('URL на открытие', 'url_open')
                ->sortable()
                ->rules('max:255')->hideFromIndex(), 
            
            Boolean::make('Автоматическое закрытие', 'auto_close'),           

            DependablePanel::make('Параметры закрытия', $this->methodCloseFields())
            ->dependsOn(
                ['auto_close'],
                function (DependablePanel $panel, NovaRequest $request, FormData $formData) {
                    if ($formData->auto_close == false) {
                        $panel->hide();
                    }
                }
            )->separatePanel(true),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [
            (new \App\Nova\Actions\SaveAllTransport)->standalone()->icon('
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>')
        ];
    }

    protected function methodCloseFields() {
        return [
            Text::make('ID потока', 'id_close_stream')
                ->sortable()
                ->rules('max:255')->hideFromIndex(),
            Text::make('URL на закрытие', 'url_close')
                ->sortable()
                ->rules('max:255')->hideFromIndex(),
            Number::make('Пауза', 'pausa')->default(60)->help('пауза перед отправкой запроса на закрытие после открытия, в сек.'),
        ];        
    }

    public static function redirectAfterCreate(NovaRequest $request, $resource)
    {
        return '/resources/'.static::uriKey();
    }

    public static function redirectAfterUpdate(NovaRequest $request, $resource)
    {
        return '/resources/'.static::uriKey();
    }
}

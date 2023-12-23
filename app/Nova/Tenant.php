<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use App\Nova\Actions\PayCkassa;
use App\Nova\Actions\PayCkassaStatus;
use Pavloniym\ActionButtons\ActionButton;
use Laravel\Nova\Fields\Currency;
use Ganyicz\NovaCallbacks\HasCallbacks;

class Tenant extends Resource
{
     use HasCallbacks;

    public static $group = '  Справочники';

    public static $priority = 1;
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Tenant>
     */
    public static $model = \App\Models\Tenant::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public static function indexQuery(NovaRequest $request, $query)
    {
        if (!$request->user()->isAdmin()  && !$request->user()->isSecurity()) {
            $tenant_id = array();
            foreach ($request->user()->tenant as $key => $value) {
                $tenant_id[] = $value->id;
            }          
            $query->whereIn('id', $tenant_id);
        }
    }

    public static function label() {
        return 'Арендаторы';
    }

    public static function singularlabel() {
        return 'Арендатор';
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
            ID::make()->sortable(),

            Text::make('Наименование', 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Эл.почта', 'email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email'),

            Text::make('Телефон', 'phone')
                ->sortable()
                ->rules('required', 'max:11'),

            Currency::make('Баланс', 'balance')->default(0)->readonly(!$request->user()->isAdmin())->rules('required', function($attribute, $value, $fail) use ($request) {
                $tenant = Tenant::find($this->id);
                if (isset($tenant->balance) && $value < $tenant->balance) {
                    return $fail('Вы не можете уменьшать баланс');
                }
            }),

            Boolean::make('Заблокирован', 'is_blocked')->hideFromIndex(),

            ActionButton::make('Оплата') // Name in resource table column
                ->icon('<svg fill="currentColor" height="21px" width="21px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 492.153 492.153" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g> <path d="M426.638,87.91c-42.247-42.247-98.418-65.514-158.166-65.514c-5.799,0-10.5,4.701-10.5,10.5v56.531 c0,5.799,4.701,10.5,10.5,10.5c80.587,0,146.148,65.561,146.148,146.147c0,80.587-65.561,146.148-146.148,146.148 c-73.915,0-135.549-54.985-144.913-127.088h36.91c0.008,0,0.013,0.001,0.02,0c5.799,0,10.5-4.701,10.5-10.5 c0-2.887-1.165-5.502-3.051-7.4l-75.345-84.401c-1.993-2.232-4.842-3.508-7.833-3.508c-0.017,0-0.034,0-0.05,0 c-3.009,0.015-5.867,1.319-7.85,3.583L2.6,247.719c-2.714,3.101-3.365,7.502-1.663,11.254c1.702,3.753,5.442,6.163,9.563,6.163 h35.11c4.553,54.02,28.36,104.134,67.69,142.033c41.883,40.359,96.99,62.587,155.171,62.587 c59.748,0,115.919-23.267,158.166-65.515c42.248-42.248,65.515-98.419,65.515-158.166 C492.153,186.328,468.886,130.157,426.638,87.91z M268.472,448.756c-109.242,0-198.191-85.45-202.501-194.535 c-0.223-5.633-4.854-10.085-10.492-10.085H33.65l51.186-58.457l52.185,58.457H112.06c-2.883,0-5.639,1.186-7.621,3.278 c-1.983,2.092-3.018,4.908-2.863,7.786c4.774,88.611,78.084,158.023,166.897,158.023c92.166,0,167.148-74.982,167.148-167.148 c0-88.639-69.355-161.384-156.648-166.821V43.665c106.9,5.479,192.181,94.173,192.181,202.41 C471.153,357.834,380.231,448.756,268.472,448.756z"></path> <path d="M255.41,255.643v79.405h-25.332c-5.799,0-10.5,4.701-10.5,10.5s4.701,10.5,10.5,10.5h25.332v13.028 c0,5.799,4.701,10.5,10.5,10.5c5.799,0,10.5-4.701,10.5-10.5v-13.964c28.222-4.984,49.733-29.669,49.733-59.3 c0-29.63-21.512-54.314-49.733-59.299v-79.407l22.119-0.001c5.799,0,10.5-4.701,10.5-10.5c0-5.799-4.701-10.5-10.5-10.5 l-22.119,0.001v-13.03c0-5.799-4.701-10.5-10.5-10.5c-5.799,0-10.5,4.701-10.5,10.5v13.965c-28.224,4.985-49.736,29.67-49.736,59.3 C205.674,225.973,227.186,250.658,255.41,255.643z M305.143,295.813c0,17.998-12.184,33.193-28.733,37.797v-75.593 C292.959,262.62,305.143,277.816,305.143,295.813z M255.41,158.545v75.595c-16.551-4.604-28.736-19.8-28.736-37.799 C226.674,178.344,238.859,163.149,255.41,158.545z"></path> </g> </g></svg>') // Svg icon (optional)
                ->text('')
                ->styles([]) // Custom css styles (optional)
                ->classes(['shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900 rounded text-sm font-bold focus:outline-none focus:ring ring-primary-200 dark:ring-gray-600 inline-flex items-center justify-center h-9 px-3 shadow relative bg-primary-500 hover:bg-primary-400 text-white dark:text-gray-900']) // Custom css classes (optional)
                ->action(new PayCkassa, $this->resource->id) // Provide action instance and resource id
                ->asToolbarButton(), // Display as row toolbar button (optional)
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
        return [ ];
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
            new PayCkassa,
        ];
    }

    public static function beforeUpdate(Request $request, $model)
    {
        if ($model->is_blocked != $request->is_blocked) {
            foreach ($model->transport as $transport) {
                $transport->access = $request->balance > 0 ? !$request->is_blocked : 0;
                $transport->save();
            }
        }
        if ($model->balance != $request->balance) {
            foreach ($model->transport as $transport) {
                $transport->access = ($request->balance > 0 && $request->is_blocked == 0)? 1 : 0;
                $transport->save();
            }
        }
    }

    public static function afterCreate(Request $request, $model) {  
          
    }

    public static function afterSave(Request $request, $model) {

    }
}

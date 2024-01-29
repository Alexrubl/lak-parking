<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Cards\Help;
use Alexrubl\Toolbar\Toolbar;
use Alexrubl\Video\Video;
use App\Models\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Dashboards\Main as Dashboard;

class Main extends Dashboard
{

    /**
     * Get the displayable name of the dashboard.
     *
     * @return string
     */
    public function name()
    {
        return 'Камеры';
    }

    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        $val = [
            (new Toolbar)->canSee(function ($request) {return Auth::user()->isAdmin() || Auth::user()->isSecurity();})
        ];
        foreach (Controller::all() as $controller) {
            if (isset($controller->cameras)) {
                foreach ($controller->cameras as $key => $camera) {
                    if ($camera["fields"]["active"] == true) {
                        $val[] = (new Video($controller, $camera))->canSee(function ($request) {
                                    return Auth::user()->isAdmin() || Auth::user()->isSecurity();
                                });
                    }
                    // break;
                }            
            }
        }
        return $val;
    }
}

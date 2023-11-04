<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Cards\Help;
use Test\Test\Test;
use Alexrubl\Video\Video;
use App\Models\Controller;
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
        return 'Панель';
    }

    /**
     * Get the cards for the dashboard.
     *
     * @return array
     */
    public function cards()
    {
        $val = [];
        foreach (Controller::all() as $controller) {
            foreach ($controller->cameras as $key => $camera) {
                $val[] = new Video($controller, $camera);
            }            
        }
        return $val;
    }
}

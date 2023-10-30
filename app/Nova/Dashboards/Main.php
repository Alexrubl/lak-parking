<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Cards\Help;
use Test\Test\Test;
use Alexrubl\Video\Video;
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
        return [
            //new Video,
            //new Test,
            //new Help,
        ];
    }
}

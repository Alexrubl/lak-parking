<?php

namespace Alexrubl\Toolbar;

use Laravel\Nova\Card;

class Toolbar extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = 'full';
    public $height = 'dynamic';

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'toolbar';
    }
}

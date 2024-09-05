<?php

namespace Alexrubl\MaskInput;

use Laravel\Nova\Fields\Field;

class MaskInput extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'mask-input';

    /**
     * Define field mask
     *
     * @param string $mask
     * @return $this
     */
    public function mask(string $mask = '')
    {
        return $this->withMeta(['mask' => $mask]);
    }
}

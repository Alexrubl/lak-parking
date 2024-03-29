<?php

namespace Laravel\Nova\Fields;

use Closure;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;

trait FormatsRelatableDisplayValues
{
    /**
     * The column that should be displayed for the field.
     *
     * @var (callable(mixed):(string))|null
     */
    public $display;

    /**
     * Format the associatable display value.
     *
     * @param  mixed  $resource
     * @return string
     */
    protected function formatDisplayValue($resource)
    {
        if (! $resource instanceof Resource) {
            $resource = Nova::newResourceFromModel($resource);
        }

        if (is_callable($this->display)) {
            return call_user_func($this->display, $resource);
        }

        return $resource->title();
    }

    /**
     * Set the column that should be displayed for the field.
     *
     * @param  (\Closure(mixed):(string))|string  $display
     * @return $this
     */
    public function display($display)
    {
        $this->display = $display instanceof Closure
                        ? $display
                        : function ($resource) use ($display) {
                            return $resource->{$display};
                        };

        return $this;
    }
}

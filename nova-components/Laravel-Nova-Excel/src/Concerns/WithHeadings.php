<?php

namespace Maatwebsite\LaravelNovaExcel\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\LaravelNovaExcel\Requests\ExportActionRequest;

trait WithHeadings
{
    /**
     * @var callable
     */
    protected $headingCallback;

    /**
     * @var array
     */
    protected $headings = [];

    /**
     * @param  array|mixed  $headings
     * @param  array  $only
     * @return $this
     */
    public function withHeadings($headings = null)
    {
        $headings = \is_array($headings) ? $headings : \func_get_args();

        if (count($headings) === 0) {
            $this->headingCallback = $this->autoHeading();
        } else {
            $this->headingCallback = function () use ($headings) {
                return $headings;
            };
        }

        return $this;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * @param  Builder  $query
     */
    protected function handleHeadings($query, ExportActionRequest $request)
    {
        if (\is_callable($this->headingCallback)) {
            $headingQuery = clone $query;
            $this->headings = ($this->headingCallback)($headingQuery, $request);
        }
    }

    protected function autoHeading(): callable
    {
        return function ($query, ExportActionRequest $request) {
            /**
             * @var Model
             */
            $model = $query->first();

            if (! $model) {
                return [];
            }

            $attributes = new Collection(
                array_keys($this->map($model))
            );

            // Attempt to replace the attribute name by the resource field name.
            // Fallback to the attribute name, when none is found.
            return $attributes->map(function (string $attribute) use ($request) {
                return $request->findHeading($attribute, $attribute);
            })->toArray();
        };
    }
}

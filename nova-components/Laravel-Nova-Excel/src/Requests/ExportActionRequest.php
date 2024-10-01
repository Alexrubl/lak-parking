<?php

namespace Maatwebsite\LaravelNovaExcel\Requests;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Resource;

interface ExportActionRequest
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|mixed
     */
    public function toExportQuery();

    public function indexFields(Resource $resource): array;

    /**
     * @return Collection|Field[]
     */
    public function resourceFields(Resource $resource): Collection;

    /**
     * @return string|null
     */
    public function findHeading(string $attribute, ?string $default = null);
}

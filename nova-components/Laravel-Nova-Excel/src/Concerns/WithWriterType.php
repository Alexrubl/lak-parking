<?php

namespace Maatwebsite\LaravelNovaExcel\Concerns;

use Laravel\Nova\Http\Requests\ActionRequest;

trait WithWriterType
{
    /**
     * @var string|null
     */
    protected $writerType;

    /**
     * @return $this
     */
    public function withWriterType(?string $writerType = null)
    {
        $this->writerType = $writerType;

        return $this;
    }

    protected function getWriterType(): ?string
    {
        return $this->writerType;
    }

    protected function handleWriterType(ActionRequest $request)
    {
        $fields = $request->resolveFields();

        if ($filename = $fields->get('writer_type')) {
            $this->withWriterType($filename);
        }
    }
}

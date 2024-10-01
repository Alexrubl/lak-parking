<?php

namespace Maatwebsite\LaravelNovaExcel\Concerns;

trait WithDisk
{
    /**
     * @var string|null
     */
    protected $disk;

    /**
     * @return $this
     */
    public function withDisk(?string $disk = null)
    {
        $this->disk = $disk;

        return $this;
    }

    protected function getDisk(): ?string
    {
        return $this->disk;
    }
}

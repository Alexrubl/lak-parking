<?php

namespace Maatwebsite\LaravelNovaExcel\Concerns;

trait WithChunkCount
{
    /**
     * @return $this
     */
    public function withChunkCount(int $chunkCount)
    {
        static::$chunkCount = $chunkCount;

        return $this;
    }

    public function chunkSize(): int
    {
        return static::$chunkCount;
    }
}

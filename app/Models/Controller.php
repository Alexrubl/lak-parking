<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class Controller extends Model
{
    use HasFactory;

    protected $table = 'controllers';

    protected $guarded = ['id'];

    protected $casts = [
        'cameras' => 'array'
    ];

    public function scopeApikey(Builder $query, string $apikey): void
    {
        $query->where('apikey', $apikey);
    }
    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $guarded = ['id'];

    public function tenant(): belongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function transport(): belongsTo
    {
        return $this->belongsTo(Transport::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Carbon\Carbon;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $guarded = ['id'];

    protected $casts = [
    // 'created_at' => 'datetime:d-m-Y H:i',
    ];

    public function tenant(): belongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function transport(): belongsTo
    {
        return $this->belongsTo(Transport::class);
    }

}

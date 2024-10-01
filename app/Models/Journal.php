<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class Journal extends Model
{
    use HasFactory;

    protected $table = 'users_journal';

    protected $guarded = ['id'];

    protected $casts = [
        // 'created_at' => 'datetime:d-m-Y H:i',
    ];

    public function user() {
        return $this->belongsTo(\App\Models\User::class);
    }
}

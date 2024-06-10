<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sigur extends Model
{
    use HasFactory;

    protected $table = 'sigur_temp';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    //protected $fillable = ['controller_id'];
    // protected $hidden = [];
}

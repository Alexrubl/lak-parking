<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;

class Rate extends Model
{
    use HasFactory;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'rates';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    // protected $fillable = [];
    // protected $hidden = [];
    protected $casts = [
        'items' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function getPrice($transport)
    {
        info_d($this);
        if ($this->type == 'Разовый') {
            foreach ($this->items as $key => $value) {
                if ($value['fields']['type_transport'] == $transport->type->id) {
                    return $value['fields']['price'];
                }
            }
        }

        if ($this->type == 'Постоянный') {
            $history = History::where('transport_id', $transport->id)
                ->where('price', '>', 0)
                ->latest()
                ->first();
            if (isset($history) && $history->created_at > Carbon::now()->startOfDay()) {
            } else {
                foreach ($this->items as $key => $value) {
                    if ($value['fields']['type_transport'] == $transport->type->id) {
                        return $value['fields']['price'];
                    }
                }
                //info('Надо списать...');
            }
        }

        if ($this->type == 'ПостоянныйМесяц') {
            $history = History::where('transport_id', $transport->id)
                ->where('price', '>', 0)
                ->latest()
                ->first();
            if (isset($history) && Carbon::parse($history->created_at) > Carbon::now()->startOfMonth()) {
            } else {
                foreach ($this->items as $key => $value) {
                    if ($value['fields']['type_transport'] == $transport->type->id) {
                        return $value['fields']['price'];
                    }
                }
                //info('Надо списать...');
            }
        }

        return 0;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function type(): belongsTo
    {
        return $this->belongsTo(TypeTransport::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}

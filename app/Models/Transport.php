<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Transport extends Model
{
    use HasFactory, SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'transports';

    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];

    //protected $fillable = ['name', 'number', 'driver','guest', 'tenant_id'];
    // protected $hidden = [];
    protected $casts = [
        'week' => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function tid()
    {
        return isset($this->uhf) ? str_replace([' ', ','], '', $this->uhf) : null;
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function tenant(): belongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function rate(): belongsTo
    {
        return $this->belongsTo(Rate::class);
    }

    public function type(): belongsTo
    {
        return $this->belongsTo(TypeTransport::class);
    }

    public function history(): hasMany
    {
        return $this->hasMany(\App\Models\History::class);
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeInside($query)
    {
        return $query->where('inside', 1);
    }

    public function scopeWithTenant($query, $tenant)
    {
        return $query->has('tenant', $tenant);
    }

    public function scopeCurrentTenant($query)
    {
        $tenant_id = [];
        foreach (Auth::user()->tenant as $key => $value) {
            $tenant_id[] = $value->id;
        }
        $query->whereIn('tenant_id', $tenant_id);
    }

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
    protected function Number(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtoupper(str_replace([' '], '', $value)),
        );
    }
}

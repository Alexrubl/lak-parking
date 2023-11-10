<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\belongsTo;
use Illuminate\Database\Eloquent\Relations\belongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function tenant() : belongsToMany
    {
        return $this->belongsToMany('App\Models\Tenant', 'user_tenant', 'user_id', 'tenant_id');
    }

    // public function tenant(): belongsTo
    // {
    //     return $this->belongsTo(Tenant::class);
    // }

    public function isAdmin()
    {
        if (in_array('Администраторы', $this->getRoleNames()->toArray()) || User::all()->count() == 1) {
            return true;
        }
        return false;
    }

    public function isTenant()
    {
        if (in_array('Арендаторы', $this->getRoleNames()->toArray())) {
            return true;
        }
        return false;
    }

    public function isSecurity()
    {
        if (in_array('Охрана', $this->getRoleNames()->toArray())) {
            return true;
        }
        return false;
    }
}

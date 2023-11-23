<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Transport;

class TransportPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

        /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Transport $transport): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $access = false;
        foreach ($user->tenant as $key => $tenant) {
            $access = true;
            if ($tenant->balance < 1) {
                $access = false;
                break;   
            }
        }
        return $user->isAdmin() || ($user->isTenant() && $access);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Transport $transport): bool
    {
        $access = false;
        foreach ($user->tenant as $key => $tenant) {
            $access = true;
            if ($tenant->balance < 1) {
                $access = false;
                break;   
            }
        }
        return $user->isAdmin() || ($user->isTenant() && $access);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Transport $transport): bool
    {
        $access = false;
        foreach ($user->tenant as $key => $tenant) {
            $access = true;
            if ($tenant->balance < 1) {
                $access = false;
                break;   
            }
        }
        return $user->isAdmin() || ($user->isTenant() && $access);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Transport $transport): bool
    {
        $access = false;
        foreach ($user->tenant as $key => $tenant) {
            $access = true;
            if ($tenant->balance < 1) {
                $access = false;
                break;   
            }
        }
        return $user->isAdmin() || ($user->isTenant() && $access);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Transport $transport): bool
    {
        return $user->isAdmin();
    }
}

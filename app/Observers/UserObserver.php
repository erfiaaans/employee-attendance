<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        if (auth()->check()) {
            $user->created_by = auth()->id();
        }
    }
    public function updating(User $user): void
    {
        if (auth()->check()) {
            $user->updated_by = auth()->id();
        }
    }
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}

<?php

namespace App\Observers;

use App\Jobs\SendRegistrationEmail;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        SendRegistrationEmail::dispatch($user)->onQueue('emails');
    }
}

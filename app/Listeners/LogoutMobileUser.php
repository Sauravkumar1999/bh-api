<?php

namespace App\Listeners;

use App\Events\UserLoggedOut;
use Laravel\Passport\Token;

class LogoutMobileUser
{
    /**
     * Handle the event.
     *
     * @param  UserLoggedOut  $event
     * @return void
     */
    public function handle(UserLoggedOut $event)
    {
        // Retrieve the user's API token by user ID and token name
        $tokens = Token::where('user_id', $event->user->id)->where('name', 'BusinessHub')->get();

        // If a token is found, delete it manually
        foreach ($tokens as $token) {
            if ($token) {
                $token->delete();
            }
        }
    }
}

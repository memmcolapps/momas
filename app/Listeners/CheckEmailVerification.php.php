<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;
use App\Models\User;

class CheckEmailVerification
{
    public function handle(MessageSending $event): bool
    {
        if (true) {//in_array(app()->environment(), ['prod', 'production', 'live', 'prd'])) {

        dd('entered');
            $to = collect($event->message->getTo())
                ->keys()
                ->first();

            $user = User::where('email', $to)->first();

            if (!$user) {
                return false;
            }

            if (!$user->email_verified_at) {
                return false;
            }

            return true;
        }

        return true;
    }
}

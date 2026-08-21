<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;
use App\Models\User;
use App\Support\RequestContext;

class CheckEmailVerification
{
    public function handle(MessageSending $event): bool
    {
        if (in_array(app()->environment(), ['prod', 'production', 'live', 'prd'])) {
            $to = collect($event->message->getTo())
                ->keys()
                ->first();

            if (app(RequestContext::class)->get('login_otp_email') || app(RequestContext::class)->get('forgot_password_email')) {
                return true;
            }

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

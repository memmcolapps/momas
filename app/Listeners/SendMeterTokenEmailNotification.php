<?php

namespace App\Listeners;

use App\Events\MeterTokenGenerated;
use App\Models\Logger;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendMeterTokenEmailNotification
{
    public function handle(MeterTokenGenerated $event): void
    {
        Logger::info('SendMeterTokenEmailNotification triggered', [
            'token_id' => $event->creditToken->id ?? null,
            'meter_no' => $event->recipientMeterNo,
        ]);

        $creditToken = $event->creditToken;
        $email = $creditToken->customer_email;
        $token = $creditToken->token;
        $vendingAmount = $event->vendingAmount;
        $kctToken1 = $event->kctToken1;
        $kctToken2 = $event->kctToken2;
        $recipientMeterNo = $event->recipientMeterNo;
        $title = $event->title;

        if ($kctToken1 && $kctToken2) {
            $this->sendKctTokenEmail($email, $token, $vendingAmount, $kctToken1, $kctToken2);
        } elseif ($recipientMeterNo) {
            $this->sendCreditTokenOthersEmail($email, $token, $vendingAmount, $recipientMeterNo, $title);
        } else {
            $this->sendCreditTokenEmail($email, $token, $vendingAmount);
        }
    }

    private function sendCreditTokenEmail(string $email, string $token, float $amount): void
    {
        $user = User::where('email', $email)->first();
        $firstName = $user?->first_name ?? 'User';

        Mail::send('emails.vendtoken', ['data1' => [
            'fromsender' => env('MAIL_FROM_ADDRESS'),
            'from_name' => 'MOMASPAY',
            'subject' => 'token Purchase',
            'toreceiver' => $email,
            'token' => $token,
            'user' => $firstName,
            'amount' => $amount
        ]], function ($message) use ($email) {
            $message->from(env('MAIL_FROM_ADDRESS'), 'MOMASPAY');
            $message->to($email);
            $message->subject('token Purchase');
        });
    }

    private function sendCreditTokenOthersEmail(string $email, string $token, float $amount, string $meterNo, ?string $title): void
    {
        $user = User::where('email', $email)->first();
        $firstName = $user?->first_name ?? 'User';

        Mail::send('emails.vendtokenothers', ['data1' => [
            'fromsender' => env('MAIL_FROM_ADDRESS'),
            'from_name' => 'MOMASPAY',
            'subject' => 'token Purchase',
            'toreceiver' => $email,
            'token' => $token,
            'user' => $firstName,
            'meterNo' => $meterNo,
            'title' => $title ?? 'Token Purchased'
        ]], function ($message) use ($email) {
            $message->from(env('MAIL_FROM_ADDRESS'), 'MOMASPAY');
            $message->to($email);
            $message->subject('token Purchase');
        });
    }

    private function sendKctTokenEmail(string $email, string $token, float $amount, string $kctToken1, string $kctToken2): void
    {
        $user = User::where('email', $email)->first();
        $firstName = $user?->first_name ?? 'User';

        Mail::send('emails.vendkcttoken', ['data1' => [
            'fromsender' => env('MAIL_FROM_ADDRESS'),
            'from_name' => 'MOMASPAY',
            'subject' => 'token Purchase',
            'toreceiver' => $email,
            'token' => $token,
            'kct_token1' => $kctToken1,
            'kct_token2' => $kctToken2,
            'user' => $firstName,
            'amount' => $amount
        ]], function ($message) use ($email) {
            $message->from(env('MAIL_FROM_ADDRESS'), 'MOMASPAY');
            $message->to($email);
            $message->subject('token Purchase');
        });
    }
}

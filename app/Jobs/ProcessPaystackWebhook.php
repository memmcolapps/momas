<?php

namespace App\Jobs;

use App\Models\CreditToken;
use App\Models\Estate;
use App\Models\Meter;
use App\Models\MeterToken;
use App\Models\Tariff;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UtilitiesPayment;
use App\Services\RequestActionHandler;
use App\Services\TokenGenerationService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\Logger;

class ProcessPaystackWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reference;

    public $tries = 5;
    public $backoff = 15;

    public function __construct($reference)
    {
        $this->reference = $reference;
    }

    public function handle(): void
    {
        // dump("Inside job");
        RequestActionHandler::handleRequestAction($this->reference);
    }

    public function failed(\Throwable $exception)
    {
        Logger::error("Job permanently failed for {$this->reference}: " . $exception->getMessage(), [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }
}

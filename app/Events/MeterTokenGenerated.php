<?php

namespace App\Events;

use App\Models\CreditToken;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeterTokenGenerated
{
    use Dispatchable, SerializesModels;

    public CreditToken $creditToken;
    public float $vendingAmount;
    public ?string $kctToken1;
    public ?string $kctToken2;
    public ?string $recipientMeterNo;
    public ?string $title;

    public function __construct(
        CreditToken $creditToken,
        float $vendingAmount,
        ?string $kctToken1 = null,
        ?string $kctToken2 = null,
        ?string $recipientMeterNo = null,
        ?string $title = null
    ) {
        $this->creditToken = $creditToken;
        $this->vendingAmount = $vendingAmount;
        $this->kctToken1 = $kctToken1;
        $this->kctToken2 = $kctToken2;
        $this->recipientMeterNo = $recipientMeterNo;
        $this->title = $title;
    }
}
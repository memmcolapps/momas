<?php

namespace App\Exceptions;

use Exception;

class InsufficientPaymentException extends Exception
{
    public function __construct(
        float $paymentAmount,
        float $totalOwed,
        string $message = '',
        int $code = 422,
        ?\Throwable $previous = null
    ) {
        if (empty($message)) {
            $message = "Payment amount of {$paymentAmount} is less than total utility owed of {$totalOwed}";
        }

        parent::__construct($message, $code, $previous);
    }
}

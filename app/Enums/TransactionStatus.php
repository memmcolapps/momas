<?php

namespace App\Enums;

enum TransactionStatus: int
{
    case PAYMENT_PENDING = 0;
    case SERVICE_PENDING = 3;
    case PAYMENT_FAILED = 1;
    case TRANSACTION_COMPLETE = 2;
}

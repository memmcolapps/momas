<?php

namespace App\Contracts;

interface PaymentServiceInterface
{
    public function getPublicKey(): ?string;

    public function getSecretKey(): ?string;

    public function makePayment(array $data): array;

    public function verifyTransaction(string|int $transactionId): array;

    public function pollTransactionStatus(
        string $reference,
        int $maxAttempts = 10,
        int $intervalSeconds = 5
    ): array;
}

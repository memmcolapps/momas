<?php

namespace App\Services;

use App\Contracts\PaymentServiceInterface;
use App\Enums\TransactionStatus;
use App\Models\Logger;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

/**
 * WalletPaymentService - Payment service implementation that works with user wallet
 *
 * This service processes payments by debiting the user's wallet balance.
 * It implements the PaymentServiceInterface for consistency with other payment providers.
 */
class WalletPaymentService implements PaymentServiceInterface
{
    /**
     * Wallet payment does not use public keys
     *
     * @return null
     */
    public function getPublicKey(): ?string
    {
        return null;
    }

    /**
     * Wallet payment does not use secret keys
     *
     * @return null
     */
    public function getSecretKey(): ?string
    {
        return null;
    }

    /**
     * Make a payment using the user's wallet balance
     *
     * Required $data keys:
     *   - amount        (numeric) Payment amount
     *   - email         (string)  User email (optional, can be derived from auth)
     *   - metadata      (array)   Additional metadata for the transaction
     *
     * Optional $data keys:
     *   - service_type  (string)  Type of service being paid for
     *   - action_payload (array) Additional payload for action handling
     *
     * @param array $data
     * @return array{status: bool, message: string, data: array|null, reference?: string}
     *
     * @throws InvalidArgumentException when required parameters are absent
     */
    public function makePayment(array $data): array
    {
        $requiredParameters = ['amount', 'email'];
        $missingParameters = array_diff($requiredParameters, array_keys($data));

        if (! empty($missingParameters)) {
            throw new InvalidArgumentException(
                'Missing required parameters: ' . implode(', ', $missingParameters)
            );
        }

        $amount = $data['amount'];
        $email = strtolower(trim($data['email']));
        $user = Auth::user();

        // Validate user matches the email
        if (!$user || strtolower($user->email) !== $email) {
            Logger::warning("Wallet payment attempted with mismatched email: {$email}");

            return [
                'status'  => false,
                'message' => 'Invalid user credentials',
                'data'    => null,
            ];
        }

        // Check sufficient wallet balance
        if ($user->main_wallet < $amount) {
            Logger::warning("Wallet payment failed - insufficient funds for user {$user->id}, amount: {$amount}, balance: {$user->main_wallet}");

            return [
                'status'  => false,
                'message' => 'Insufficient wallet balance',
                'data'    => [
                    'required_amount' => $amount,
                    'available_balance' => $user->main_wallet,
                ],
            ];
        }

        try {
            // Generate unique transaction reference
            $transactionRef = generate_unique_string('WALLET');

            // Debit the user's wallet
            $user->debitWallet($amount);

            // // Create transaction record
            // $trx = new Transaction();
            // $trx->user_id = $user->id;
            // $trx->pay_type = 'wallet';
            // $trx->estate_id = $user->estate_id;
            // $trx->amount = $amount;
            // $trx->trx_id = $transactionRef;
            // $trx->payment_ref = $transactionRef;
            // $trx->service_type = $data['service_type'] ?? 'wallet_payment';
            // $trx->status = 2; // Completed status
            // $trx->action_payload = json_encode($data['action_payload'] ?? []);
            // $trx->save();

            Logger::info("Wallet payment completed successfully", [
                'user_id' => $user->id,
                'amount' => $amount,
                'reference' => $transactionRef,
            ]);

            return [
                'status'    => true,
                'message'   => 'Wallet payment completed successfully',
                'data'      => [
                    'reference' => $transactionRef,
                    'amount' => $amount,
                    'balance' => $user->main_wallet,
                    'payment_status' => 'successful',
                ],
                'reference' => $transactionRef,
            ];
        } catch (Exception $e) {
            Logger::error("Wallet payment failed: " . $e->getMessage(), [
                'user_id' => $user->id,
                'amount' => $amount,
            ]);

            return [
                'status'  => false,
                'message' => 'Wallet payment failed: ' . $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    /**
     * Verify a wallet transaction
     *
     * For wallet payments, verification is immediate since the payment
     * is processed synchronously during makePayment.
     *
     * @param string|int $transactionId The transaction reference to verify
     * @return array Verification result
     */
    public function verifyTransaction(string|int $transactionId): array
    {
        try {
            // Find the transaction by reference
            $transaction = Transaction::where('trx_id', $transactionId)
                ->where('pay_type', 'wallet')
                ->first();

            if (!$transaction) {
                return [
                    'status'         => false,
                    'message'        => 'Transaction not found',
                    'payment_status' => null,
                ];
            }

            // For wallet payments, status 2 means completed/successful
            $isSuccessful = $transaction->status === TransactionStatus::SERVICE_PENDING->value;
            $paymentStatus = $isSuccessful ? 'success' : 'failed';

            return [
                'status'         => true,
                'message'        => $isSuccessful ? 'Transaction completed successfully' : 'Transaction failed',
                'payment_status' => $paymentStatus,
                'is_successful'  => $isSuccessful,
                'data'           => [
                    'reference'    => $transaction->trx_id,
                    'amount'       => $transaction->amount,
                    'currency'     => 'NGN',
                    'customer_email' => $transaction->user->email ?? '',
                    'status'       => $paymentStatus,
                    'paid_at'      => $transaction->updated_at?->toIso8601String(),
                ],
            ];
        } catch (Exception $e) {
            return [
                'status'         => false,
                'message'        => 'Transaction verification failed: ' . $e->getMessage(),
                'payment_status' => null,
            ];
        }
    }

    /**
     * Poll transaction status of a wallet transaction
     *
     * For wallet payments, this returns immediate success since the payment
     * is processed synchronously.
     *
     * @param string $transactionReference
     * @param int $maxAttempts
     * @param int $intervalSeconds
     * @return array
     */
    public function pollTransactionStatus(
        string $transactionReference,
        int $maxAttempts = 10,
        int $intervalSeconds = 5
    ): array {
        // For wallet payments, verification is immediate
        // Return the verification result directly
        $verificationResult = $this->verifyTransaction($transactionReference);

        if ($verificationResult['status']) {
            return [
                'status'         => true,
                'message'       => $verificationResult['message'],
                'payment_status' => $verificationResult['payment_status'],
                'is_successful'  => $verificationResult['is_successful'] ?? false,
                'data'           => $verificationResult['data'] ?? null,
            ];
        }

        return [
            'status'         => false,
            'message'        => 'Failed to poll transaction status',
            'payment_status' => null,
            'is_successful'  => false,
            'data'           => null,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Auditlog;
use App\Models\Logger;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Throwable;

/**
 * Audit log service.
 *
 * Single entry point:
 *
 *     Audit::action('meter.updated', $meter);
 *     Audit::action('tariff.updated', $tariff, ['section' => 'nepa']);
 *
 * Action identifiers are defined in {@see Audit::definitions()} as closures
 * receiving ($actor, $model, $request, $context) and returning the
 * human-readable interpretation, e.g. "Ada updated meter 42".
 *
 * The interpretation plus actor, request and model diff metadata is
 * persisted to the `auditlogs` table. Logging never breaks the request:
 * unknown actions and persistence failures are reported via Logger and
 * resolve to null.
 */
class Audit
{
    /**
     * Keys never persisted to old_values / new_values.
     */
    private const REDACTED_KEYS = [
        'password',
        'password_confirmation',
        'secret',
        'secret_key',
        'secret_id',
        'bvn',
        'nin',
        'otp',
        'pin',
        'token',
        'id_string',
        'credentials',
    ];

    /**
     * Action string => interpreter closure.
     *
     * Closure signature: function ($actor, Model $model, $request, array $context): string
     *
     * Kept in a method (rather than a class constant / property default)
     * because PHP constant expressions cannot contain closures.
     */
    protected static function definitions(): array
    {
        return [
            // ── Authentication (AuthController / LoginController / RegisterController) ──
            'auth.signed_in' => function ($actor, Model $model): string {
                return sprintf('%s signed in', self::actorName($actor));
            },
            'auth.signed_out' => function ($actor, Model $model): string {
                return sprintf('%s signed out', self::actorName($actor));
            },
            'auth.login_failed' => function ($actor, Model $model, $request, array $context): string {
                $email = $context['email'] ?? ($request ? $request->input('email') : null) ?? 'unknown user';

                return sprintf('Failed sign-in attempt for %s', $email);
            },
            'auth.registered' => function ($actor, Model $model): string {
                return sprintf('%s registered an account', self::actorName($actor));
            },
            'auth.password_reset_requested' => function ($actor, Model $model, $request, array $context): string {
                $email = $context['email'] ?? ($request ? $request->input('email') : null) ?? self::modelId($model);

                return sprintf('%s requested a password reset for %s', self::actorName($actor), $email);
            },
            'auth.password_reset' => function ($actor, Model $model): string {
                return sprintf('%s reset their password', self::actorName($actor));
            },
            'auth.2fa_enabled' => function ($actor, Model $model): string {
                return sprintf('%s enabled two-factor authentication', self::actorName($actor));
            },

            // ── Users / organisations (DashboardContoller) ──
            'user.created' => function ($actor, Model $model): string {
                return sprintf('%s created user %s', self::actorName($actor), self::modelId($model));
            },
            'user.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated user %s', self::actorName($actor), self::modelId($model));
            },
            'user.deleted' => function ($actor, Model $model): string {
                return sprintf('%s deleted user %s', self::actorName($actor), self::modelId($model));
            },
            'user.activated' => function ($actor, Model $model): string {
                return sprintf('%s activated user %s', self::actorName($actor), self::modelId($model));
            },
            'user.deactivated' => function ($actor, Model $model): string {
                return sprintf('%s deactivated user %s', self::actorName($actor), self::modelId($model));
            },
            'user.password_updated' => function ($actor, Model $model): string {
                return sprintf('%s updated the password for user %s', self::actorName($actor), self::modelId($model));
            },
            'user.email_updated' => function ($actor, Model $model, $request, array $context): string {
                $email = $context['email'] ?? ($request ? $request->input('email') : null) ?? '?';

                return sprintf('%s updated the email for user %s to %s', self::actorName($actor), self::modelId($model), $email);
            },
            'user.viewed' => function ($actor, Model $model): string {
                return sprintf('%s viewed user %s', self::actorName($actor), self::modelId($model));
            },
            'organization.created' => function ($actor, Model $model): string {
                return sprintf('%s created organisation %s', self::actorName($actor), self::modelId($model));
            },
            'organization.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated organisation %s', self::actorName($actor), self::modelId($model));
            },
            'organization.deleted' => function ($actor, Model $model): string {
                return sprintf('%s deleted organisation %s', self::actorName($actor), self::modelId($model));
            },
            'organization.activated' => function ($actor, Model $model): string {
                return sprintf('%s activated organisation %s', self::actorName($actor), self::modelId($model));
            },
            'organization.deactivated' => function ($actor, Model $model): string {
                return sprintf('%s deactivated organisation %s', self::actorName($actor), self::modelId($model));
            },
            'organization.percentage_updated' => function ($actor, Model $model, $request, array $context): string {
                $percentage = $context['percentage'] ?? ($request ? $request->input('percentage') : null) ?? '?';

                return sprintf('%s updated the percentage for organisation %s to %s', self::actorName($actor), self::modelId($model), $percentage);
            },

            // ── Estates (Admin/EstateController) ──
            'estate.created' => function ($actor, Model $model): string {
                return sprintf('%s created estate %s', self::actorName($actor), self::modelId($model));
            },
            'estate.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated estate %s', self::actorName($actor), self::modelId($model));
            },
            'estate.deleted' => function ($actor, Model $model): string {
                return sprintf('%s deleted estate %s', self::actorName($actor), self::modelId($model));
            },
            'estate.viewed' => function ($actor, Model $model): string {
                return sprintf('%s viewed estate %s', self::actorName($actor), self::modelId($model));
            },
            'estate.default_set' => function ($actor, Model $model): string {
                return sprintf('%s set estate %s as their default estate', self::actorName($actor), self::modelId($model));
            },
            'estate.activated' => function ($actor, Model $model): string {
                return sprintf('%s activated estate %s', self::actorName($actor), self::modelId($model));
            },
            'estate.deactivated' => function ($actor, Model $model): string {
                return sprintf('%s deactivated estate %s', self::actorName($actor), self::modelId($model));
            },
            'estate.tariff_updated' => function ($actor, Model $model): string {
                return sprintf('%s updated the tariff configuration for estate %s', self::actorName($actor), self::modelId($model));
            },
            'estate.utilities_updated' => function ($actor, Model $model): string {
                return sprintf('%s updated the utilities for estate %s', self::actorName($actor), self::modelId($model));
            },
            'estate.features_updated' => function ($actor, Model $model): string {
                return sprintf('%s updated the features for estate %s', self::actorName($actor), self::modelId($model));
            },
            'estate.duration_updated' => function ($actor, Model $model, $request, array $context): string {
                $duration = $context['duration'] ?? ($request ? $request->input('duration') : null) ?? '?';

                return sprintf('%s updated the duration for estate %s to %s', self::actorName($actor), self::modelId($model), $duration);
            },
            'estate.vat_updated' => function ($actor, Model $model, $request, array $context): string {
                $vat = $context['vat'] ?? ($request ? $request->input('vat') : null) ?? '?';

                return sprintf('%s updated VAT for estate %s to %s', self::actorName($actor), self::modelId($model), $vat);
            },
            'estate.min_purchase_updated' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['min_purchase'] ?? ($request ? $request->input('min_purchase') : null) ?? '?';

                return sprintf('%s updated the minimum purchase for estate %s to %s', self::actorName($actor), self::modelId($model), $amount);
            },
            'estate.fee_accumulation_updated' => function ($actor, Model $model, $request, array $context): string {
                $period = $context['fee_accumulation_period'] ?? ($request ? $request->input('fee_accumulation_period') : null) ?? '?';

                return sprintf('%s updated fee accumulation for estate %s to %s', self::actorName($actor), self::modelId($model), $period);
            },
            'estate.transaction_fee_updated' => function ($actor, Model $model, $request, array $context): string {
                $fee = $context['transaction_fee'] ?? ($request ? $request->input('transaction_fee') : null) ?? '?';

                return sprintf('%s updated the transaction fee for estate %s to %s', self::actorName($actor), self::modelId($model), $fee);
            },
            'estate.payment_gateways_updated' => function ($actor, Model $model, $request, array $context): string {
                $gateways = $context['gateways'] ?? ($request ? $request->input('gateways') : null) ?? 'gateways';
                $gateways = is_array($gateways) ? implode(', ', $gateways) : $gateways;

                return sprintf('%s updated the payment gateways for estate %s (%s)', self::actorName($actor), self::modelId($model), $gateways);
            },

            // ── Meters and tokens (Admin/MeterController) ──
            'meter.created' => function ($actor, Model $model): string {
                return sprintf('%s created meter %s', self::actorName($actor), self::modelId($model));
            },
            'meter.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated meter %s', self::actorName($actor), self::modelId($model));
            },
            'meter.deleted' => function ($actor, Model $model): string {
                return sprintf('%s deleted meter %s', self::actorName($actor), self::modelId($model));
            },
            'meter.viewed' => function ($actor, Model $model): string {
                return sprintf('%s viewed meter %s', self::actorName($actor), self::modelId($model));
            },
            'meter.activated' => function ($actor, Model $model): string {
                return sprintf('%s activated meter %s', self::actorName($actor), self::modelId($model));
            },
            'meter.deactivated' => function ($actor, Model $model): string {
                return sprintf('%s deactivated meter %s', self::actorName($actor), self::modelId($model));
            },
            'meter.blocked' => function ($actor, Model $model): string {
                return sprintf('%s blocked meter %s', self::actorName($actor), self::modelId($model));
            },
            'meter.unblocked' => function ($actor, Model $model): string {
                return sprintf('%s unblocked meter %s', self::actorName($actor), self::modelId($model));
            },
            'meter.estate_migrated' => function ($actor, Model $model, $request, array $context): string {
                $estate = $context['estate_name'] ?? $context['estate_id'] ?? ($request ? $request->input('estate_id') : null) ?? '?';

                return sprintf('%s migrated meter %s to estate %s', self::actorName($actor), self::modelId($model), $estate);
            },
            'meter.detached' => function ($actor, Model $model): string {
                return sprintf('%s detached meter %s', self::actorName($actor), self::modelId($model));
            },
            'meter.imported' => function ($actor, Model $model, $request, array $context): string {
                $count = $context['count'] ?? '?';

                return sprintf('%s imported %s meter(s)', self::actorName($actor), $count);
            },
            'token.generated' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';

                return sprintf('%s generated a token for meter %s (amount: %s)', self::actorName($actor), self::modelId($model), $amount);
            },
            'token.kct_generated' => function ($actor, Model $model): string {
                return sprintf('%s generated a KCT token for meter %s', self::actorName($actor), self::modelId($model));
            },
            'token.emergency_generated' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';

                return sprintf('%s generated an emergency token for meter %s (amount: %s)', self::actorName($actor), self::modelId($model), $amount);
            },
            'token.retried' => function ($actor, Model $model): string {
                return sprintf('%s retried token generation for meter %s', self::actorName($actor), self::modelId($model));
            },

            // ── Tariffs (Admin/TariffController) ──
            'tariff.created' => function ($actor, Model $model): string {
                return sprintf('%s created tariff %s', self::actorName($actor), self::modelId($model));
            },
            'tariff.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated tariff %s', self::actorName($actor), self::modelId($model));
            },
            'tariff.deleted' => function ($actor, Model $model): string {
                return sprintf('%s deleted tariff %s', self::actorName($actor), self::modelId($model));
            },
            'tariff.viewed' => function ($actor, Model $model): string {
                return sprintf('%s viewed tariff %s', self::actorName($actor), self::modelId($model));
            },
            'tariff.activated' => function ($actor, Model $model): string {
                return sprintf('%s activated tariff %s', self::actorName($actor), self::modelId($model));
            },
            'tariff.deactivated' => function ($actor, Model $model): string {
                return sprintf('%s deactivated tariff %s', self::actorName($actor), self::modelId($model));
            },
            'tariff.state_updated' => function ($actor, Model $model): string {
                return sprintf('%s updated state tariff %s', self::actorName($actor), self::modelId($model));
            },
            'tariff.index_updated' => function ($actor, Model $model): string {
                return sprintf('%s updated tariff index %s', self::actorName($actor), self::modelId($model));
            },
            'tariff.nepa_updated' => function ($actor, Model $model): string {
                return sprintf('%s updated the NEPA tariff %s', self::actorName($actor), self::modelId($model));
            },
            'tariff.gen_updated' => function ($actor, Model $model): string {
                return sprintf('%s updated the generator tariff %s', self::actorName($actor), self::modelId($model));
            },
            'tariff.nepa_detached' => function ($actor, Model $model): string {
                return sprintf('%s detached the NEPA tariff from %s', self::actorName($actor), self::modelId($model));
            },
            'tariff.gen_detached' => function ($actor, Model $model): string {
                return sprintf('%s detached the generator tariff from %s', self::actorName($actor), self::modelId($model));
            },

            // ── Utilities and transactions (Dashboard / TransactionController) ──
            'utility.created' => function ($actor, Model $model): string {
                return sprintf('%s created utility %s', self::actorName($actor), self::modelId($model));
            },
            'utility.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated utility %s', self::actorName($actor), self::modelId($model));
            },
            'utility.deleted' => function ($actor, Model $model): string {
                return sprintf('%s deleted utility %s', self::actorName($actor), self::modelId($model));
            },
            'utility.paid' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';

                return sprintf('%s marked utility %s as paid (amount: %s)', self::actorName($actor), self::modelId($model), $amount);
            },
            'utility.unpaid' => function ($actor, Model $model): string {
                return sprintf('%s marked utility %s as unpaid', self::actorName($actor), self::modelId($model));
            },
            'utility.payment_cleared' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';

                return sprintf('%s cleared utility payment %s (amount: %s)', self::actorName($actor), self::modelId($model), $amount);
            },
            'utility.accumulation_cleared' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';

                return sprintf('%s cleared all postpaid accumulation fees for estate %s (amount: %s)', self::actorName($actor), self::modelId($model), $amount);
            },
            'utility.accumulation_partially_cleared' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';

                return sprintf('%s partially cleared postpaid accumulation fees for estate %s (amount: %s)', self::actorName($actor), self::modelId($model), $amount);
            },
            'utility.payment_edited' => function ($actor, Model $model): string {
                return sprintf('%s edited utility payment %s', self::actorName($actor), self::modelId($model));
            },
            'utility.customer_detached' => function ($actor, Model $model): string {
                return sprintf('%s detached customer from utility %s', self::actorName($actor), self::modelId($model));
            },
            'customer.created' => function ($actor, Model $model): string {
                return sprintf('%s created customer %s', self::actorName($actor), self::modelId($model));
            },
            'customer.imported' => function ($actor, Model $model, $request, array $context): string {
                $count = $context['count'] ?? '?';

                return sprintf('%s imported %s customer(s)', self::actorName($actor), $count);
            },
            'customer.debt_payment' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';

                return sprintf('%s recorded a debt payment of %s for customer %s', self::actorName($actor), $amount, self::modelId($model));
            },
            'wallet.funded' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';

                return sprintf('%s funded wallet %s with %s', self::actorName($actor), self::modelId($model), $amount);
            },
            'payment.initiated' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';
                $reference = $context['reference'] ?? ($request ? $request->input('reference') : null) ?? '?';

                return sprintf('%s initiated payment %s (amount: %s, reference: %s)', self::actorName($actor), self::modelId($model), $amount, $reference);
            },
            'payment.verified' => function ($actor, Model $model, $request, array $context): string {
                $reference = $context['reference'] ?? ($request ? $request->input('reference') : null) ?? '?';

                return sprintf('%s verified payment %s (reference: %s)', self::actorName($actor), self::modelId($model), $reference);
            },
            'payment.failed' => function ($actor, Model $model, $request, array $context): string {
                $reason = $context['reason'] ?? ($request ? $request->input('reason') : null) ?? 'no reason given';

                return sprintf('%s recorded a failed payment %s. Reason: %s', self::actorName($actor), self::modelId($model), $reason);
            },
            'payment.webhook_received' => function ($actor, Model $model, $request, array $context): string {
                $provider = $context['provider'] ?? 'payment provider';

                return sprintf('Received %s webhook for %s', $provider, self::modelId($model));
            },
            'bill.airtime_purchased' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';

                return sprintf('%s purchased airtime (amount: %s)', self::actorName($actor), $amount);
            },
            'bill.data_purchased' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';

                return sprintf('%s purchased data (amount: %s)', self::actorName($actor), $amount);
            },
            'bill.cable_purchased' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';

                return sprintf('%s purchased a cable subscription (amount: %s)', self::actorName($actor), $amount);
            },
            'arrears.paid' => function ($actor, Model $model, $request, array $context): string {
                $amount = $context['amount'] ?? ($request ? $request->input('amount') : null) ?? '?';

                return sprintf('%s paid arrears %s (amount: %s)', self::actorName($actor), self::modelId($model), $amount);
            },

            // ── Estate services, issues, alerts and beneficiaries ──
            'service.created' => function ($actor, Model $model): string {
                return sprintf('%s created service %s', self::actorName($actor), self::modelId($model));
            },
            'service.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated service %s', self::actorName($actor), self::modelId($model));
            },
            'service.viewed' => function ($actor, Model $model): string {
                return sprintf('%s viewed estate service %s', self::actorName($actor), self::modelId($model));
            },
            'service.deleted' => function ($actor, Model $model): string {
                return sprintf('%s deleted service %s', self::actorName($actor), self::modelId($model));
            },
            'service.comment_deleted' => function ($actor, Model $model): string {
                return sprintf('%s deleted comment %s', self::actorName($actor), self::modelId($model));
            },
            'service.activated' => function ($actor, Model $model): string {
                return sprintf('%s activated service %s', self::actorName($actor), self::modelId($model));
            },
            'service.deactivated' => function ($actor, Model $model): string {
                return sprintf('%s deactivated service %s', self::actorName($actor), self::modelId($model));
            },
            'issue.created' => function ($actor, Model $model): string {
                return sprintf('%s created issue %s', self::actorName($actor), self::modelId($model));
            },
            'issue.viewed' => function ($actor, Model $model): string {
                return sprintf('%s viewed issue %s', self::actorName($actor), self::modelId($model));
            },
            'issue.resolver_assigned' => function ($actor, Model $model, $request, array $context): string {
                $resolver = $context['user_id'] ?? ($request ? $request->input('user_id') : null) ?? 'a resolver';

                return sprintf('%s assigned issue %s to %s', self::actorName($actor), self::modelId($model), $resolver);
            },
            'issue.status_updated' => function ($actor, Model $model, $request, array $context): string {
                $status = $context['status'] ?? ($request ? $request->input('status') : null) ?? '?';

                return sprintf('%s updated issue %s status to %s', self::actorName($actor), self::modelId($model), $status);
            },
            'issue.deleted' => function ($actor, Model $model): string {
                return sprintf('%s deleted issue %s', self::actorName($actor), self::modelId($model));
            },
            'alert.created' => function ($actor, Model $model, $request, array $context): string {
                $title = $context['title'] ?? ($request ? $request->input('title') : null) ?? '?';

                return sprintf('%s created alert %s (%s)', self::actorName($actor), self::modelId($model), $title);
            },
            'beneficiary.created' => function ($actor, Model $model): string {
                return sprintf('%s created beneficiary %s', self::actorName($actor), self::modelId($model));
            },
            'beneficiary.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated beneficiary %s', self::actorName($actor), self::modelId($model));
            },
            'beneficiary.deleted' => function ($actor, Model $model): string {
                return sprintf('%s deleted beneficiary %s', self::actorName($actor), self::modelId($model));
            },
            'beneficiary.activated' => function ($actor, Model $model): string {
                return sprintf('%s activated beneficiary %s', self::actorName($actor), self::modelId($model));
            },
            'beneficiary.deactivated' => function ($actor, Model $model): string {
                return sprintf('%s deactivated beneficiary %s', self::actorName($actor), self::modelId($model));
            },

            // ── Access tokens, assets and transformers ──
            'access_token.created' => function ($actor, Model $model): string {
                return sprintf('%s created access token %s', self::actorName($actor), self::modelId($model));
            },
            'access_token.revoked' => function ($actor, Model $model): string {
                return sprintf('%s revoked access token %s', self::actorName($actor), self::modelId($model));
            },
            'access_token.activated' => function ($actor, Model $model): string {
                return sprintf('%s activated access token %s', self::actorName($actor), self::modelId($model));
            },
            'access_token.deactivated' => function ($actor, Model $model): string {
                return sprintf('%s deactivated access token %s', self::actorName($actor), self::modelId($model));
            },
            'asset.created' => function ($actor, Model $model): string {
                return sprintf('%s created asset %s', self::actorName($actor), self::modelId($model));
            },
            'asset.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated asset %s', self::actorName($actor), self::modelId($model));
            },
            'asset.deleted' => function ($actor, Model $model): string {
                return sprintf('%s deleted asset %s', self::actorName($actor), self::modelId($model));
            },
            'transformer.created' => function ($actor, Model $model): string {
                return sprintf('%s created transformer %s', self::actorName($actor), self::modelId($model));
            },
            'transformer.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated transformer %s', self::actorName($actor), self::modelId($model));
            },
            'transformer.deleted' => function ($actor, Model $model): string {
                return sprintf('%s deleted transformer %s', self::actorName($actor), self::modelId($model));
            },
            'transformer.activated' => function ($actor, Model $model): string {
                return sprintf('%s activated transformer %s', self::actorName($actor), self::modelId($model));
            },
            'transformer.deactivated' => function ($actor, Model $model): string {
                return sprintf('%s deactivated transformer %s', self::actorName($actor), self::modelId($model));
            },

            // ── Settings (DashboardContoller / AppSettingController) ──
            'settings.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated system settings', self::actorName($actor));
            },
            'feature.updated' => function ($actor, Model $model, $request, array $context): string {
                $section = $context['section'] ?? ($request ? $request->input('section') : null) ?? 'features';

                return sprintf('%s updated system features (%s)', self::actorName($actor), $section);
            },
            'payment_keys_updated' => function ($actor, Model $model): string {
                return sprintf('%s updated payment keys', self::actorName($actor));
            },
            'admin_fee_updated' => function ($actor, Model $model, $request, array $context): string {
                $fee = $context['admin_fee'] ?? ($request ? $request->input('admin_fee') : null) ?? '?';

                return sprintf('%s updated the admin fee to %s', self::actorName($actor), $fee);
            },
            'support.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated support settings', self::actorName($actor));
            },
            'config.updated' => function ($actor, Model $model): string {
                return sprintf('%s updated system configuration', self::actorName($actor));
            },
            'app_version.updated' => function ($actor, Model $model, $request, array $context): string {
                $version = $context['version'] ?? ($request ? $request->input('version') : null) ?? '?';

                return sprintf('%s updated the app version to %s', self::actorName($actor), $version);
            },
        ];
    }

    /**
     * All registered action identifiers.
     */
    public static function actions(): array
    {
        return array_keys(self::definitions());
    }

    /**
     * Resolve a short user_type alias (user, admin, customer) or a
     * fully-qualified model class name to the stored user_type value.
     *
     * @throws InvalidArgumentException on unknown types.
     */
    public static function userClass(string $userType): string
    {
        $normalized = strtolower(trim($userType));

        if (in_array($normalized, ['user', 'admin', 'customer', 'staff'], true)) {
            return User::class;
        }

        $trimmed = trim($userType);
        if (str_contains($trimmed, '\\') && class_exists(ltrim($trimmed, '\\'))) {
            return ltrim($trimmed, '\\');
        }

        if (strtolower(class_basename(User::class)) === $normalized) {
            return User::class;
        }

        throw new InvalidArgumentException(
            "Unknown user_type: {$userType}. Expected one of: user, admin, customer."
        );
    }

    /**
     * Resolve a user_type to all stored user_type values to match.
     *
     * @return array<int, string>
     *
     * @throws InvalidArgumentException on unknown types.
     */
    public static function userClasses(string $userType): array
    {
        return [self::userClass($userType)];
    }

    /**
     * Base query for audit logs belonging to a given user.
     *
     * Returns a builder so callers can add estate scope, extra
     * filters, ordering and pagination:
     *
     *     Audit::forUser('user', $id)->where('estate_id', $estateId)->latest()->paginate();
     *
     * @throws InvalidArgumentException on unknown user_type or empty user_id.
     */
    public static function forUser(string $userType, mixed $userId): Builder
    {
        $classes = self::userClasses($userType);

        if ($userId === null) {
            throw new InvalidArgumentException('user_id is required.');
        }

        $id = is_string($userId) ? trim($userId) : (string) $userId;

        if ($id === '') {
            throw new InvalidArgumentException('user_id is required.');
        }

        $query = Auditlog::where('user_id', $id);

        if (count($classes) === 1) {
            $query->where('user_type', $classes[0]);
        } else {
            $query->whereIn('user_type', $classes);
        }

        return $query;
    }

    /**
     * Interpret an action against a model and persist it to the audit logs.
     *
     * Call after save/refresh so getChanges() is populated:
     *
     *     $meter->save();
     *     Audit::action('meter.updated', $meter->fresh());
     *
     * Extra interpolation data (reason, amount, section, ...) can be passed
     * via $context. Never throws: returns null when the action is unknown
     * or persistence fails.
     */
    public static function action(string $action, Model $model, array $context = []): ?Auditlog
    {
        $definitions = self::definitions();

        if (! isset($definitions[$action])) {
            self::report('warning', "Unknown audit action: {$action}", [
                'action' => $action,
                'entity_type' => get_class($model),
                'entity_id' => self::modelId($model),
            ]);

            return null;
        }

        try {
            $request = app()->bound('request') ? request() : null;
            $actor = (isset($context['actor']) && is_object($context['actor']))
                ? $context['actor']
                : self::resolveActor($request);

            $interpreted = self::interpret($definitions[$action], $actor, $model, $request, $context);

            [$oldValues, $newValues] = self::changedValues($model);

            return Auditlog::create([
                'estate_id' => self::resolveEstateId($actor, $model, $request, $context),
                'user_id' => self::stringOrNull(self::actorId($actor)),
                'user_type' => $actor ? get_class($actor) : null,
                'user_name' => $actor ? self::actorName($actor) : null,
                'action' => $interpreted,
                'action_identifier' => $action,
                'entity_type' => get_class($model),
                'entity_id' => self::stringOrNull($model->getKey()),
                'description' => $interpreted,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $request?->ip(),
                'user_agent' => self::truncate($request?->userAgent(), 1000),
            ]);
        } catch (Throwable $e) {
            self::report('warning', "Audit logging failed for action: {$action}", [
                'action' => $action,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Run an interpreter closure with a generic fallback.
     */
    protected static function interpret(callable $interpreter, $actor, Model $model, $request, array $context): string
    {
        try {
            $description = $interpreter($actor, $model, $request, $context);
        } catch (Throwable $e) {
            $description = null;
        }

        if (! is_string($description) || $description === '') {
            $description = sprintf('%s performed %s on %s %s', self::actorName($actor), 'action', class_basename($model), self::modelId($model));
        }

        return $description;
    }

    /**
     * Current authenticated actor (User), if any.
     */
    protected static function resolveActor($request)
    {
        $candidates = [
            $request?->attributes?->get('auth_user'),
            $request?->attributes?->get('actor'),
        ];

        foreach ($candidates as $candidate) {
            if (is_object($candidate)) {
                return $candidate;
            }
        }

        try {
            if (auth()->check()) {
                return auth()->user();
            }
        } catch (Throwable $e) {
            // fall through to named guards
        }

        foreach (['web', 'api'] as $guard) {
            try {
                if (auth($guard)->check()) {
                    return auth($guard)->user();
                }
            } catch (Throwable $e) {
                continue;
            }
        }

        return null;
    }

    protected static function resolveEstateId($actor, Model $model, $request, array $context): ?string
    {
        if (isset($context['estate_id'])) {
            return (string) $context['estate_id'];
        }

        if ($actor) {
            if (method_exists($actor, 'getEstateId')) {
                try {
                    $value = $actor->getEstateId();
                    if ($value) {
                        return (string) $value;
                    }
                } catch (Throwable $e) {
                    // fall through to model / request
                }
            }

            try {
                $value = $actor->estate_id ?? null;
                if ($value) {
                    return (string) $value;
                }
            } catch (Throwable $e) {
                // fall through to model / request
            }
        }

        foreach (['estate_id', 'business_id', 'businessId'] as $key) {
            $fromModel = $model->getAttribute($key);
            if ($fromModel) {
                return (string) $fromModel;
            }
        }

        if ($request) {
            try {
                $fromRequest = $request->input('estate_id')
                    ?? $request->input('business_id')
                    ?? $request->input('businessId');
                if ($fromRequest) {
                    return (string) $fromRequest;
                }
            } catch (Throwable $e) {
                // ignore request access failures
            }
        }

        return null;
    }

    protected static function actorName($actor): string
    {
        if (! is_object($actor)) {
            return 'System';
        }

        if (method_exists($actor, 'getName')) {
            try {
                $name = trim((string) $actor->getName());
                if ($name !== '') {
                    return $name;
                }
            } catch (Throwable $e) {
                // fall through
            }
        }

        $parts = ['first_name' => '', 'last_name' => ''];

        foreach (['first_name', 'last_name'] as $key) {
            try {
                $parts[$key] = trim((string) ($actor->{$key} ?? ''));
            } catch (Throwable $e) {
                // ignore
            }
        }

        $fullName = trim($parts['first_name'] . ' ' . $parts['last_name']);
        if ($fullName !== '') {
            return $fullName;
        }

        foreach (['email', 'business_email', 'name'] as $key) {
            try {
                $value = $actor->{$key} ?? null;
                if (is_string($value) && $value !== '') {
                    return $value;
                }
            } catch (Throwable $e) {
                continue;
            }
        }

        return 'System';
    }

    protected static function actorId($actor): mixed
    {
        if (! is_object($actor)) {
            return null;
        }

        if (method_exists($actor, 'getActorId')) {
            try {
                return $actor->getActorId();
            } catch (Throwable $e) {
                // fall through
            }
        }

        return $actor->id ?? null;
    }

    protected static function modelId(Model $model): string
    {
        return (string) ($model->getKey() ?? 'unknown');
    }

    /**
     * Old and new values for updated attributes only.
     *
     * Returns [old, new] where old holds the pre-change values of exactly
     * the attributes present in the change set, i.e.
     * array_intersect_key($model->getOriginal(), $changes).
     *
     * Falls back to getDirty() when getChanges() is empty (pre-save model).
     * Returns [null, null] when nothing changed.
     */
    protected static function changedValues(Model $model): array
    {
        try {
            $changes = $model->getChanges();
            if (empty($changes)) {
                $changes = $model->getDirty();
            }
        } catch (Throwable $e) {
            return [null, null];
        }

        if (empty($changes)) {
            return [null, null];
        }

        try {
            $old = array_intersect_key($model->getOriginal(), $changes);
        } catch (Throwable $e) {
            $old = [];
        }

        return [
            empty($old) ? null : self::sanitize($old),
            self::sanitize($changes),
        ];
    }

    protected static function sanitize(mixed $data): ?array
    {
        try {
            $decoded = json_decode(json_encode($data), true);
        } catch (Throwable $e) {
            return null;
        }

        if (! is_array($decoded)) {
            return null;
        }

        foreach (self::REDACTED_KEYS as $key) {
            if (array_key_exists($key, $decoded)) {
                $decoded[$key] = '[redacted]';
            }
        }

        array_walk_recursive($decoded, function (&$value) {
            if (is_resource($value)) {
                $value = '[resource:' . get_resource_type($value) . ']';
            } elseif ($value instanceof \Closure) {
                $value = '[closure]';
            } elseif (is_string($value) && ! mb_check_encoding($value, 'UTF-8')) {
                $value = mb_scrub($value, 'UTF-8');
            }
        });

        return $decoded;
    }

    /**
     * auditlogs.user_id / entity_id are string columns. Actors and some
     * entities use uuid keys — persist them as strings, casting legacy
     * integers. Values longer than 36 characters are truncated.
     */
    protected static function stringOrNull(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $string = (string) $value;

        if ($string === '') {
            return null;
        }

        return mb_strlen($string) > 36 ? mb_substr($string, 0, 36) : $string;
    }

    protected static function truncate(?string $value, int $limit): ?string
    {
        if ($value === null) {
            return null;
        }

        return mb_strlen($value) > $limit ? mb_substr($value, 0, $limit) : $value;
    }

    protected static function report(string $level, string $message, array $context = []): void
    {
        try {
            if ($level === 'warning') {
                Logger::warning($message, $context);
            } else {
                Logger::error($message, $context);
            }
        } catch (Throwable $e) {
            // Logging must never break the request.
        }
    }
}

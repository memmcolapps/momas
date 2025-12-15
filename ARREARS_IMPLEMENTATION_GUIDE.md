# ARREARS TRACKING SYSTEM - IMPLEMENTATION GUIDE

## ✅ COMPLETED (Ready to Use)

### 1. Database Schema
- ✅ `customer_arrears` table migration
- ✅ `estates.arrears_payment_percentage` field (0-100%)
- ✅ `users.service_charge_amount` field (per-customer charge)
- ✅ `settings.default_arrears_percentage` field (global default)

### 2. Models & Services
- ✅ `CustomerArrear` model with relationships and helper methods
- ✅ `ArrearsService` helper class with all business logic

### 3. Cron Job
- ✅ `GenerateMonthlyArrears` command (runs daily, 5-day grace period)
- ✅ Scheduler configured in `Kernel.php`

---

## 🔧 REMAINING INTEGRATION STEPS

### Step 1: Run Migrations

```bash
cd /mnt/disks/gatsby/momas/momas
php artisan migrate
```

This will create:
- `customer_arrears` table
- Add config fields to `estates`, `users`, `settings`

---

### Step 2: Integrate into TokenController

Add this to the top of `TokenController.php`:

```php
use App\Models\CustomerArrear;
use App\Services\ArrearsService;
use Carbon\Carbon;
```

Find the `validate_meter` method (around line 500-600) and add **BEFORE** the calculation logic:

```php
// === ARREARS & SERVICE CHARGE LOGIC ===
$customer = User::find($user->id);
$estate = Estate::find($estate_id);

// 1. Check for outstanding arrears
$totalArrears = ArrearsService::getTotalArrears($customer->id);
$minArrearsPayment = ArrearsService::calculateMinimumPayment($customer->id, $estate_id);

// 2. Check if can afford purchase
$affordability = ArrearsService::canAffordPurchase($customer->id, $estate_id, $request->amount);

if (!$affordability['can_afford']) {
    return back()->with('error', sprintf(
        'Insufficient amount. You have ₦%s in arrears (%d%% = ₦%s) + ₦%s minimum purchase = ₦%s required.',
        number_format($totalArrears, 2),
        $estate->arrears_payment_percentage,
        number_format($minArrearsPayment, 2),
        number_format($estate->min_pur, 2),
        number_format($affordability['required_amount'], 2)
    ));
}

// 3. Get customer service charge
$customerServiceCharge = $customer->service_charge_amount ?? 0;

// 4. Check if first transaction of month (for fixed charge)
$isFirstTransaction = !ArrearsService::hadTransactionThisMonth($customer->id);
```

Then modify the calculation flow (around line 540-560):

```php
// NEW CALCULATION FLOW WITH ARREARS:
// [1] 2.5% Service Fee
$percn = (2.5 / 100) * (int)$request->amount;
$afterServiceFee = $request->amount - $percn;

// [2] Estate Service Charge
$est = Estate::where('id', $estate_id)->first();
if ($est->charge_fee_flat != null) {
    $estateFee = $est->charge_fee_flat;
} else if ($est->charge_fee_precent != null) {
    $estateFee = ($est->charge_fee_precent / 100) * (int)$request->amount;
} else {
    $estateFee = 0;
}
$afterEstateFee = $afterServiceFee - $estateFee;

// [3] Customer Service Charge (NEW!)
$afterCustomerCharge = $afterEstateFee - $customerServiceCharge;

// [4] Fixed Charge (only if first transaction of month)
$fixedChargeApplied = 0;
if ($isFirstTransaction && $fixedCharge > 0) {
    $afterFixedCharge = $afterCustomerCharge - $fixedCharge;
    $fixedChargeApplied = $fixedCharge;
} else {
    $afterFixedCharge = $afterCustomerCharge;
}

// [5] Arrears Payment (minimum percentage)
$arrearsDeducted = 0;
if ($minArrearsPayment > 0) {
    $arrearsDeducted = $minArrearsPayment;
    $afterArrears = $afterFixedCharge - $minArrearsPayment;
} else {
    $afterArrears = $afterFixedCharge;
}

// [6] VAT Calculation on remaining amount
$calculator = new VatCalculator();
$params = [
    'amountText' => $afterArrears,
    'tariffAmount' => $tariffAmount,
    'utilitiesAmount' => 0,
    'vat' => $vat,
];

$vatAmount = $calculator->calculateVatAmount($params);
$costOfUnit = $calculator->calculateCostOfUnit($params);
$tariffPerKWatt = $calculator->calculateTariffAmountPerKWatt($params);

// Pass to view
$data['vatAmount'] = $vatAmount;
$data['costOfUnit'] = $costOfUnit;
$data['tariffPerKWatt'] = $tariffPerKWatt;
$data['user'] = $user;
$data['meter'] = $meter;
$data['estate'] = $est;
$data['preview'] = "on";
$data['amount'] = $request->amount;
$data['vat'] = $vat;
$data['estate_id'] = $estate_id;
$data['estate_name'] = $request->estate_id;
$data['tarrif_amount'] = $tariffAmount;
$data['tarrif_index'] = $tariffState->t_index ?? null;
$data['credit_tokens'] = CreditToken::latest()->paginate('50');
$data['estateFee'] = $estateFee;
$data['fixedCharge'] = $fixedChargeApplied;
$data['serviceFee'] = $percn;
$data['customerServiceCharge'] = $customerServiceCharge; // NEW
$data['arrearsDeducted'] = $arrearsDeducted; // NEW
$data['totalArrears'] = $totalArrears; // NEW

return view('admin.token.credit-token-preview', $data);
```

---

### Step 3: Process Arrears After Successful Payment

Find where tokens are generated (after payment success), add:

```php
// After successful payment, process arrears deduction
if ($arrearsDeducted > 0) {
    ArrearsService::processArrearsPayment(
        $customer->id,
        $arrearsDeducted,
        $transaction->id
    );
}
```

---

### Step 4: Update Preview View (Optional)

In `credit-token-preview.blade.php`, show arrears breakdown:

```html
@if(isset($totalArrears) && $totalArrears > 0)
<div class="alert alert-warning">
    <strong>Outstanding Arrears:</strong> ₦{{ number_format($totalArrears, 2) }}<br>
    <strong>Arrears Payment ({{ $estate->arrears_payment_percentage }}%):</strong> ₦{{ number_format($arrearsDeducted, 2) }}
</div>
@endif

@if(isset($customerServiceCharge) && $customerServiceCharge > 0)
<p><strong>Your Service Charge:</strong> ₦{{ number_format($customerServiceCharge, 2) }}</p>
@endif
```

---

## 📊 TESTING STEPS

### 1. Test Migration
```bash
php artisan migrate
```

### 2. Test Arrears Generation
```bash
php artisan arrears:generate
```

### 3. Test Arrears Service
Create a test script in `tinker`:
```bash
php artisan tinker
```

```php
$user = User::find(55);
ArrearsService::getTotalArrears($user->id);
ArrearsService::getArrearsSummary($user->id);
```

### 4. Test Vending with Arrears
1. Manually create an arrear for a customer
2. Try to purchase token
3. Verify arrears percentage is deducted
4. Check arrears table updated

---

## 🔧 CONFIGURATION

### Set Arrears Percentage per Estate

```sql
-- Set Estate 5 to require 40% arrears payment
UPDATE estates SET arrears_payment_percentage = 40 WHERE id = 5;

-- Set Estate 10 to require full payment (strict)
UPDATE estates SET arrears_payment_percentage = 100 WHERE id = 10;

-- Set Estate 13 to make arrears optional
UPDATE estates SET arrears_payment_percentage = 0 WHERE id = 13;
```

### Set Customer Service Charge

```sql
-- Add 50 NGN service charge to specific customer
UPDATE users SET service_charge_amount = 50.00 WHERE id = 55;
```

---

## 🚀 PRODUCTION DEPLOYMENT

### 1. Backup Database
```bash
docker exec momas_db_local mysqldump -u root -psimplepassword momas > backup_$(date +%Y%m%d).sql
```

### 2. Run Migrations
```bash
php artisan migrate --force
```

### 3. Enable Cron (Laravel Scheduler)
Add to crontab inside your Docker container:
```bash
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

Or in docker-compose.yml, ensure scheduler is running.

---

## 📈 VENDING FLOW SUMMARY

```
Customer Purchase: ₦1,000
│
├─ [1] Platform Fee (2.5%):        -25    → ₦975
├─ [2] Estate Fee:                -200    → ₦775
├─ [3] Customer Service Charge:    -50    → ₦725
├─ [4] Fixed Charge (if 1st):     -100    → ₦625 (only first transaction/month)
├─ [5] Arrears (40%):             -150    → ₦475 (percentage-based)
├─ [6] VAT:                        -79    → ₦396
├─ [7] ÷ Tariff Rate:            ÷ 20
└─ = Units:                      19.8 kWh
```

---

## ⚠️ IMPORTANT NOTES

1. **Arrears Generation**: Runs daily but only generates after 5th day of month
2. **Fixed Charge**: Only charged on FIRST transaction of month
3. **Percentage**: Configurable per estate (0-100%)
4. **FIFO**: Oldest arrears paid first
5. **Utilities**: Tracked separately, NOT auto-deducted (keep existing flow)

---

## 🆘 TROUBLESHOOTING

### Arrears not generating?
```bash
php artisan arrears:generate --verbose
```

### Check cron schedule:
```bash
php artisan schedule:list
```

### View customer arrears:
```sql
SELECT * FROM customer_arrears WHERE user_id = 55;
```

---

## 📞 NEXT STEPS

1. ✅ Run migrations
2. ⏳ Integrate Token Controller code above
3. ⏳ Test with sample customer
4. ⏳ Deploy to production
5. ⏳ Monitor arrears generation daily

---

**Implementation Time Estimate:** 30-60 minutes for integration + testing

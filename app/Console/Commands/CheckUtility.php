<?php

namespace App\Console\Commands;

use App\Models\Estate;
use App\Models\UtilitiesPayment;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Logger;

class CheckUtility extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-utility';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $utilityPayments = UtilitiesPayment::where('type', 'utilities')->get();

        foreach ($utilityPayments as $payment) {
            $estate = Estate::find($payment->estate_id);

            if (!$estate) {

                $mssage = "Estate not found for UtilityPayment ID: {$payment->id}";
                send_notification($mssage);

                Logger::warning("Estate not found for UtilityPayment ID: {$payment->id}");
                continue;


            }

            $nextDueDate = Carbon::parse($payment->next_due_date);
            switch ($payment->duration) {
                case 'weekly':
                    $nextDueDate->addWeek();
                    break;
                case 'monthly':
                    $nextDueDate->addMonth();
                    break;
                case 'yearly':
                    $nextDueDate->addYear();
                    break;
                default:


                    $mssage = "Unknown duration '{$payment->duration}' for UtilityPayment ID: {$payment->id}";
                    send_notification($mssage);


            }

            DB::beginTransaction();
            try {

                $newAmount = $payment->amount + $estate->total_utility_amount;

                $newPayment = UtilitiesPayment::create([
                    'type'          => 'utilities',
                    'amount'        => $payment->amount,
                    'total_amount'  => $newAmount,
                    'duration'      => $payment->duration,
                    'next_due_date' => $nextDueDate,
                    'estate_id'     => $payment->estate_id
                ]);


                $mssage = "Updated UtilityPayment ID: {$payment->id} | New Amount: {$payment->amount} | Next Due Date: {$payment->next_due_date}";
                send_notification($mssage);

                Logger::info("Updated UtilityPayment ID: {$payment->id} | New Amount: {$payment->amount} | Next Due Date: {$payment->next_due_date}");

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                Logger::error("Error updating UtilityPayment ID: {$payment->id} - " . $e->getMessage());
            }
        }

        $this->info('Utility payment check completed.');



    }
}

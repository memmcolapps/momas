<?php

namespace App\Console\Commands;

use App\Models\Estate;
use App\Models\User;
use Illuminate\Console\Command;

class FixDedupEmails extends Command
{
    protected $signature = 'user:fix-dedup-emails {--dry-run : Preview changes without writing}';

    protected $description = 'Restore original emails to users whose legacy-import _dupXXXXXX dedup suffix is not a real duplicate';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $estates = Estate::whereNotNull('legacy_buid')
            ->where('legacy_buid', '!=', '')
            ->get();

        if ($estates->isEmpty()) {
            $this->warn('No estates found with a legacy_buid.');
            return self::SUCCESS;
        }

        $this->info(
            ($dryRun ? '[DRY-RUN] ' : '') . "Processing {$estates->count()} estate(s) with a legacy_buid..."
        );

        $matched = 0;
        $restored = 0;
        $skipped = 0;

        foreach ($estates as $estate) {
            $users = User::where('estate_id', $estate->id)
                ->where('email', 'regexp', '^.+_dup[a-zA-Z0-9]{6}@.+$')
                ->get();

            foreach ($users as $user) {
                if (!preg_match('/^(.+)_dup[a-zA-Z0-9]{6}@(.+)$/', $user->email, $matches)) {
                    continue;
                }

                $matched++;

                $original = $matches[1] . '@' . $matches[2];

                $taken = User::where('email', $original)
                    ->where('id', '!=', $user->id)
                    ->exists();

                if ($taken) {
                    $this->line("  user {$user->id} [{$user->email}]: skipped (original {$original} already taken)");
                    $skipped++;
                    continue;
                }

                $this->line("  user {$user->id} [{$user->email}]: -> {$original}");

                if (!$dryRun) {
                    $user->update(['email' => $original]);
                }

                $restored++;
            }
        }

        $this->newLine();
        $this->info("Done: {$matched} dedup email(s) found, {$restored} restored, {$skipped} skipped (real duplicates).");

        return self::SUCCESS;
    }
}

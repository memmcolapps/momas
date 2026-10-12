<?php

namespace App\Console\Commands;

use App\Models\Bank;
use Illuminate\Console\Command;

class SyncRemitaBanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:sync-remita {--dry-run : Preview changes without writing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update or create bank records with Remita bank codes';

    /**
     * Remita bank codes mapped to their bank names.
     */
    private const REMITA_BANKS = [
        '000' => 'CENTRAL BANK OF NIGERIA',
        '011' => 'FIRST BANK OF NIGERIA PLC',
        '023' => 'NIGERIA INTERNATIONAL BANK (CITIBANK)',
        '030' => 'BANK PLC',
        '032' => 'UNION BANK OF NIGERIA PLC',
        '033' => 'UBA PLC',
        '035' => 'WEMA BANK PLC',
        '039' => 'STANBIC IBTC BANK PLC',
        '044' => 'ACCESS BANK PLC',
        '050' => 'ECOBANK NIGERIA PLC',
        '057' => 'ZENITH BANK PLC',
        '058' => 'GUARANTY TRUST BANK',
        '068' => 'STANDARD CHARTERED BANK NIGERIA LTD',
        '070' => 'FIDELITY BANK PLC',
        '076' => 'SKYE BANK PLC',
        '101' => 'PROVIDUS BANK',
        '214' => 'FIRST CITY MONUMENT BANK PLC',
        '215' => 'UNITY BANK PLC',
        '232' => 'STERLING BANK PLC',
        '301' => 'JAIZ BANK',
        '459' => 'CORONATION MERCHANT BANK LIMITED',
        '480' => 'JUBILEE BANK',
        '510120013' => 'KANO MICROFINANCE BANKS',
        '511080016' => 'ESO SAVINGS LOANS PLC',
        '511080026' => 'ASO SAVINGS AND LOANS',
        '511080036' => 'NATIONAL HOUSING FUND',
        '511080106' => 'GAA-AKANBI MICRO FINANCE BANK',
        '512170012' => 'CLASSIC MICROFINANCE BANK',
        '512170022' => 'SOLID ROCK MFB OKE ONA ABEOKUTA',
        '512170032' => 'LAVENDER MICROFINANCE BANK LTD',
        '512170042' => 'EBIGI MICROFINANCE BANK',
        '512170052' => 'EFOTAMODI/OGUNOLA MICROFINANCE BANK',
        '512170062' => 'EIYEPE MICROFINANCE BANK',
        '512170072' => 'EJOSE MICROFINANCE BANK LTD',
        '512170082' => 'EMAZING GRACE MICROFINANCE BANK',
        '512170092' => 'CATLAND MICROFINANCE BANK LTD',
        '512170102' => 'OGUN STATE MICROFINANCE BANKS',
        '512170112' => 'UKUOMBE MICROFINANCE BANK LTD',
        '512170122' => 'URUWON MICROFINANCE BANK',
        '512170132' => 'USO-E MICROFINANCE BANK',
        '512170142' => 'EPPLE MICROFINANCE BANK',
        '512170152' => 'OFONYIN MICROFINANCE BANK',
        '512170162' => 'OMODI-IMOSAN MICROFINANCE BANK LTD',
        '512170172' => 'OJEBU-IFE COMMUNITY BANK NIG. LTD',
        '512170182' => 'OJEBU-IMUSIN MICROFINANCE BANK LTD',
        '512170192' => 'OKENNE MICROFINANCE BANK LTD',
        '512170202' => 'OGUN STATE MICROFINANCE BANKS',
        '512170212' => 'OLISAN MICROFINANCE BANK LTD',
        '512170222' => 'OMOWO MICROFINANCE BANK NIG LTD',
        '512170232' => 'ONTERLAND MICROFINANCE BANK',
        '512170242' => 'OPERU MICROFINANCE BANK LTD',
        '512170252' => 'OTELE MICROFINANCE BANK LTD',
        '512170262' => 'OGUN STATE MICROFINANCE BANKS',
        '512170272' => 'OGUN STATE MICROFINANCE BANKS',
        '512170282' => 'OGUN STATE MICROFINANCE BANKS',
        '512170292' => 'MALPOLY MICROFINANCE BANK',
        '512170302' => 'MOLUSI MICROFINANCE BANK LTD',
        '512170312' => 'NEW IMAGE MFB EGBA ODEDA',
        '512170322' => 'COMBINED BENEFITS MICROFINANCE BANK',
        '512170332' => 'OGUN STATE MICROFINANCE BANKS',
        '512170342' => 'AMU COMMUNITY BANK LTD',
        '512170352' => 'ARISUN MFB',
        '512170362' => 'RIVERSIDE MICROFINANCE BANK LTD',
        '512170372' => 'SAGAM MICROFINANCE BANK',
        '512170382' => 'TRUST MFB EGBA OWODE ABEOKUTA',
        '512170392' => 'OGUN STATE MICROFINANCE BANKS',
        '512170402' => 'ACON SUCCESS MICROFINANCE BANK LTD',
        '512170412' => 'UNAAB MICROFINANCE BANK',
        '512170422' => 'WEST-END MICROFINANCE BANK',
        '512170432' => 'HONEY MICROFINANCE BANK',
        '512170442' => 'EGOSASA MICROFINANCE BANK',
        '512170452' => 'ESTRA POLARIS MICROFINANCE BANK LTD',
        '512170462' => 'OGUN STATE MICROFINANCE BANKS',
        '512170472' => 'OGUN STATE MICROFINANCE BANKS',
        '512170492' => 'STAR MICROFINANCE BANK',
        '512170502' => 'CENTAGE SAVINGS AND LOANS',
        '512170522' => 'OGUN STATE MICROFINANCE BANKS',
        '512170542' => 'GATEWAY SAVINGS AND LOANS LTD',
        '512170552' => 'NACRDB ABIGI',
        '512170562' => 'NACRDB AYETORO',
        '512170572' => 'NACRDB IMEKO',
        '512170582' => 'NACRDB ABEOKUTA',
        '512170592' => 'OGUN STATE MICROFINANCE BANKS',
        '512170602' => 'NACRDB OTA',
        '512170612' => 'NACRDB ODEDA ABEOKUTA',
        '512170622' => 'NACRDB AGO-IWOYE',
        '512170632' => 'NACRDB SAGAM',
        '512170652' => 'ONTEGRATED MICROFINANCE BANK',
        '512170662' => 'OROLU MIRCOFINANCE BANK LTD',
        '513210013' => 'RIVERS MICROFINANCE BANKS',
        '514040013' => 'EDO MICROFINANCE BANKS',
        '515150013' => 'LAGOS BUILDING INVESTMENT CO.LTD',
        '515150023' => 'SKYE MORTGAGE',
        '515150033' => 'UNION HOMES SAVINGS AND LOANS PLC',
        '515150043' => 'NIGERIA POLICE FORCE',
        '515150053' => 'NIGERIA POLICE FORCE',
        '515150063' => 'LAGOS MICROFINANCE BANKS',
        '515150073' => 'SPECSMFB',
        '516290013' => 'OSUN MICROFINANCE BANKS',
        '516290023' => 'AAU MICRO-FINANCE BANK',
        '517150014' => 'ARITA BASHORUN MICROFINANCE BANK',
        '517150024' => 'MULTIVEST MICROFINANCE BANK',
        '517150034' => 'OGBOORA MFB',
        '517150044' => 'PACESETTERS MICROFINANCE BANK',
        '517150054' => 'CIVIC MICROFINANCE BANK',
        '517150064' => 'ALOGBON MFB',
        '517150074' => 'CREST MFB',
        '517150084' => 'EYETE MFB',
        '517150094' => 'EKESAN MFB',
        '518150014' => 'YOBE SAVINGS & LOANS LIMITED',
        '518150024' => 'NIG AGRIC COOP & RURAL DEV BANK LTD',
        '518150034' => 'YOBE MICROFINANCE BANK',
        '518150044' => 'YOBE MICROFINANCE BANK',
        '518150054' => 'YOBE MICROFINANCE BANK',
        '518150064' => 'YOBE MICROFINANCE BANK',
        '519290019' => 'ZION MFB',
        '520150010' => 'AURS MICRO FINANCE BANK',
        '520150020' => 'KWARA STATE MICROFINANCE BANKS',
        '520150030' => 'KWARA STATE MICROFINANCE BANKS',
        '520150040' => 'KWARA STATE MICROFINANCE BANKS',
        '520150050' => 'KWARA STATE MICROFINANCE BANKS',
        '520150060' => 'KWARA STATE MICROFINANCE BANKS',
        '520150070' => 'KWARA STATE MICROFINANCE BANKS',
        '520150080' => 'KWARA STATE MICROFINANCE BANKS',
        '520150090' => 'KWARA STATE MICROFINANCE BANKS',
        '520150100' => 'KWARA STATE MICROFINANCE BANKS',
        '520150110' => 'KWARA STATE MICROFINANCE BANKS',
        '520150120' => 'KWARA STATE MICROFINANCE BANKS',
        '520150130' => 'KWARA STATE MICROFINANCE BANKS',
        '520150140' => 'KWARA STATE MICROFINANCE BANKS',
        '520150150' => 'KWARA STATE MICROFINANCE BANKS',
        '520150160' => 'KWARA STATE MICROFINANCE BANKS',
        '520150170' => 'KWARA STATE MICROFINANCE BANKS',
        '520150180' => 'KWARA STATE MICROFINANCE BANKS',
        '521150010' => 'OKOLE MICROFINANCE BANK',
        '521150020' => 'SUNBEAM MICROFINANCE BANK LTD',
        '521150030' => 'ERAMOKO MFB LIMITED',
        '522150010' => 'JIGAWA SAVINGS & LOANS LTD',
        '580000010' => 'JUBILEE LIFE MORTGAGE BANK',
        '590000001' => 'POCKET MONEY',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $this->info(
            ($dryRun ? '[DRY-RUN] ' : '').'Syncing '.count(self::REMITA_BANKS).' Remita banks...'
        );

        $created = 0;
        $updated = 0;
        $unchanged = 0;

        $claimedCodes = [];
        $claimedBankIds = [];
        $claimedNames = [];

        foreach (self::REMITA_BANKS as $code => $bankName) {
            $code = (string) $code;

            if (array_key_exists($code, $claimedCodes)) {
                $this->line("  {$code} {$bankName} -> already set");
                $unchanged++;

                continue;
            }

            $bank = $this->findBank($code, $bankName, $claimedBankIds, $claimedNames);

            if (! $bank) {
                $this->line("  {$code} {$bankName} -> CREATE");

                if (! $dryRun) {
                    $bank = Bank::create([
                        'name' => $bankName,
                        'bankName' => $bankName,
                        'remita_code' => $code,
                    ]);
                    $claimedBankIds[] = $bank->id;
                }

                $claimedCodes[$code] = true;
                $claimedNames[$this->normalizeName($bankName)] = true;
                $created++;

                continue;
            }

            if ($bank->remita_code === $code) {
                $this->line("  {$code} {$bankName} -> already set");
                $unchanged++;

                continue;
            }

            $this->line("  {$code} {$bankName} -> UPDATE (bank #{$bank->id})");

            $claimedCodes[$code] = true;
            $claimedBankIds[] = $bank->id;

            if (! $dryRun) {
                Bank::where('id', $bank->id)->update(['remita_code' => $code]);
            }

            $updated++;
        }

        $this->info("Created: {$created} | Updated: {$updated} | Unchanged: {$unchanged}");

        return self::SUCCESS;
    }

    /**
     * Find an existing bank for a Remita entry, or return null.
     *
     * @param  array<int, int>  $claimedBankIds
     * @param  array<string, true>  $claimedNames
     */
    private function findBank(string $code, string $bankName, array $claimedBankIds = [], array $claimedNames = []): ?Bank
    {
        $bank = Bank::where('remita_code', $code)->first();

        if ($bank) {
            return $bank;
        }

        $bank = Bank::where('code', $code)->first();

        if ($bank) {
            return $bank;
        }

        if (ctype_digit($code)) {
            $match = Bank::where('code', 'REGEXP', '^[0-9]+$')
                ->get()
                ->first(fn ($bank) => (int) $bank->code === (int) $code);

            if ($match) {
                return $match;
            }
        }

        $normalized = $this->normalizeName($bankName);

        if ($normalized !== '') {
            $match = Bank::whereNull('remita_code')
                ->whereNotIn('id', $claimedBankIds)
                ->get()
                ->first(function ($bank) use ($normalized, $claimedNames) {
                    foreach (array_filter([$bank->name, $bank->bankName]) as $candidate) {
                        $candidateName = $this->normalizeName($candidate);

                        if ($candidateName === $normalized && ! isset($claimedNames[$candidateName])) {
                            return true;
                        }
                    }

                    return false;
                });

            if ($match) {
                return $match;
            }
        }

        return null;
    }

    /**
     * Normalize a bank name for comparison.
     */
    private function normalizeName(string $name): string
    {
        $name = mb_strtoupper($name);
        $name = str_replace(['-', '/', '&', ',', '.', "'", '"', '(', ')'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', trim($name)) ?? '';
        $name = preg_replace('/\bMFB\b/', 'MICROFINANCE', $name) ?? '';

        $words = array_filter(explode(' ', $name), function ($word) {
            return ! in_array($word, ['PLC', 'LTD', 'LIMITED', 'INC', 'CO', 'NIG', 'NIGERIA', 'BANK', 'BANKS'], true);
        });

        return implode(' ', array_values($words));
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Estate;
use App\Models\Transformer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncLegacyTransformers extends Command
{
    protected $signature = 'legacy:sync-transformers
                            {--dry-run : Preview changes without writing}
                            {--file=   : Read an existing transformers.json instead of re-exporting}';

    protected $description = 'Export all legacy ECMI transformers, then update our transformers that match on Name + Location with the estate resolved from BUID';

    public function handle(): int
    {
        $path = storage_path('app/legacy-export');

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $file = $this->option('file');

        if ($file) {
            $this->info("Reading transformers from: {$file}");
            $rows = $this->read($file);
        } else {
            $this->info('Exporting all legacy transformers to transformers.json...');
            $rows = $this->exportTransformers($path);
        }

        $this->info('Export finished successfully');

        // BUID (normalised) → estate id
        $estates = Estate::whereNotNull('legacy_buid')
            ->where('legacy_buid', '!=', '')
            ->get(['id', 'legacy_buid']);

        $estateMap = [];
        foreach ($estates as $estate) {
            $estateMap[$this->normalize($estate->legacy_buid)] = $estate->id;
        }

        // Index our transformers by normalised Title + Location
        $ours = Transformer::all(['id', 'Title', 'Location']);
        $oursByKey = [];
        foreach ($ours as $transformer) {
            $key = $this->matchKey($transformer->Title, $transformer->Location);
            $oursByKey[$key][] = $transformer;
        }

        $stats = [
            'records'             => count($rows),
            'updated'             => 0,
            'estate_not_found'    => 0,
            'no_transformer_match' => 0,
        ];

        foreach ($rows as $row) {
            $key = $this->matchKey($row->Name, $row->Location);

            $matches = $oursByKey[$key] ?? null;

            if (!$matches) {
                $this->warn("[WARN] No transformer match on our system for legacy '{$row->Name}' / '{$row->Location}' (TransID={$row->TransID})");
                $stats['no_transformer_match']++;
                continue;
            }

            $estateId = $estateMap[$this->normalize($row->BUID ?? '')] ?? null;

            if (!$estateId) {
                $this->warn("[WARN] No estate with legacy_buid '{$row->BUID}' for '{$row->Name}' (TransID={$row->TransID}) — leaving Estate_id unchanged");
                $stats['estate_not_found']++;
            }

            foreach ($matches as $transformer) {
                $updates = ['legacy_trans_id' => $row->TransID];

                if ($estateId) {
                    $updates['Estate_id'] = $estateId;
                }

                if ($this->isDryRun()) {
                    $this->preview("Transformer id={$transformer->id} '{$transformer->Title}'", $updates);
                    continue;
                }

                Transformer::where('id', $transformer->id)->update($updates);
            }

            $stats['updated']++;
        }

        $this->newLine();
        $this->table(['Metric', 'Count'], [
            ['Legacy records',         $stats['records']],
            ['Records with a match',   $stats['updated']],
            ['Estate not found (BUID)', $stats['estate_not_found']],
            ['No transformer match',   $stats['no_transformer_match']],
        ]);

        $this->info('Transformer sync finished successfully.');

        return self::SUCCESS;
    }

    protected function exportTransformers(string $path): array
    {
        $rows = DB::connection('mssql_legacy')
            ->table('Transformer')
            ->get()
            ->toArray();

        $this->write('transformers.json', $rows, $path);

        $this->info('Exported ' . count($rows) . ' transformers → transformers.json');

        return $rows;
    }

    protected function read(string $file): array
    {
        if (!file_exists($file)) {
            throw new \RuntimeException("Required export file not found: {$file}");
        }

        return json_decode(file_get_contents($file), false, 512, JSON_THROW_ON_ERROR);
    }

    protected function write(string $file, array $data, string $path)
    {
        file_put_contents(
            $path . '/' . $file,
            json_encode($data, JSON_PRETTY_PRINT)
        );
    }

    protected function normalize($value): string
    {
        return strtolower(trim(preg_replace('/\s+/', ' ', (string) $value)));
    }

    protected function matchKey($name, $location): string
    {
        return $this->normalize($name) . '||' . $this->normalize($location);
    }

    protected function isDryRun(): bool
    {
        return (bool) $this->option('dry-run');
    }

    protected function preview(string $type, array $data): void
    {
        $this->line('=====================================');
        $this->info("[DRY RUN] {$type}");
        $this->line(json_encode($data, JSON_PRETTY_PRINT));
        $this->line('=====================================');
    }
}

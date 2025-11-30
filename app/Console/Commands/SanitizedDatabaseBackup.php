<?php

namespace App\Console\Commands;

use App\Support\Security\DataMasker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class SanitizedDatabaseBackup extends Command
{
    protected $signature = 'db:backup:sanitized {--path= : Custom output directory}';

    protected $description = 'Create a sanitized backup with PII masked in configured tables.';

    public function handle(): int
    {
        $tables = config('security.backup.tables', []);
        if (empty($tables)) {
            $this->warn('Tidak ada tabel yang dikonfigurasi untuk backup tersanitasi.');
            return self::SUCCESS;
        }

        $outputDir = $this->option('path') ?: config('security.backup.output_path');
        File::ensureDirectoryExists($outputDir);

        $filePath = rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'sanitized_backup_' . now()->format('Ymd_His') . '.json';

        $payload = [
            'meta' => [
                'generated_at' => now()->toIso8601String(),
                'connection' => config('database.default'),
                'masked_columns' => $tables,
            ],
            'data' => [],
        ];

        foreach ($tables as $table => $maskedColumns) {
            $this->info("Menyalin tabel {$table} dengan masking kolom: " . implode(', ', $maskedColumns));
            $payload['data'][$table] = $this->exportTable($table, $maskedColumns);
        }

        file_put_contents($filePath, json_encode($payload, JSON_PRETTY_PRINT));
        $this->info("Backup tersanitasi disimpan di: {$filePath}");

        return self::SUCCESS;
    }

    protected function exportTable(string $table, array $maskedColumns): array
    {
        $chunkSize = (int) config('security.backup.chunk', 500);
        $rows = [];

        $orderColumn = Schema::hasColumn($table, 'id') ? 'id' : null;
        $query = DB::table($table);

        if ($orderColumn) {
            $query->orderBy($orderColumn)->chunk($chunkSize, function ($chunk) use (&$rows, $maskedColumns) {
                $this->collectChunk($chunk, $rows, $maskedColumns);
            });
        } else {
            foreach ($query->cursor() as $row) {
                $this->collectChunk([$row], $rows, $maskedColumns);
            }
        }

        return $rows;
    }

    protected function collectChunk(iterable $chunk, array &$rows, array $maskedColumns): void
    {
        $maskedKeys = array_map('strtolower', $maskedColumns);

        foreach ($chunk as $row) {
            $record = (array) $row;
            foreach ($record as $key => $value) {
                $lowerKey = strtolower((string) $key);
                if (in_array($lowerKey, $maskedKeys, true)) {
                    $record[$key] = $value === null ? null : DataMasker::maskString((string) $value, [$key], true);
                } else {
                    $record[$key] = DataMasker::mask($value);
                }
            }
            $rows[] = $record;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillTaskSlots extends Command
{
    protected $signature = 'tasks:backfill-slots {--dry-run : Hanya hitung tanpa menulis data}';

    protected $description = 'Buat slot default 100% untuk task lama yang belum memiliki slot sama sekali.';

    public function handle(): int
    {
        $dry = $this->option('dry-run');

        $query = Task::doesntHave('slots');
        $count = $query->count();
        if ($count === 0) {
            $this->info('Tidak ada task yang perlu di-backfill.');
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$count} task tanpa slot.");

        if ($dry) {
            $this->info('Dry-run selesai. Tidak ada data yang ditulis.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $query->chunkById(100, function ($tasks) use ($bar) {
            DB::transaction(function () use ($tasks, $bar) {
                foreach ($tasks as $task) {
                    $minutes = $task->duration_minutes ?? 0;
                    $task->slots()->create([
                        'name' => 'Default',
                        'percentage' => 100,
                        'minutes' => $minutes > 0 ? $minutes : 1,
                        'order' => 0,
                        'created_by' => null,
                        'status' => 'approved', // anggap selesai sesuai progres task lama
                        'approved_by' => $task->assigned_by,
                        'approved_at' => now(),
                        'rejection_reason' => null,
                    ]);
                    $task->recalcProgressFromSlots();
                    $bar->advance();
                }
            });
        });

        $bar->finish();
        $this->newLine(2);
        $this->info('Backfill selesai.');

        return Command::SUCCESS;
    }
}

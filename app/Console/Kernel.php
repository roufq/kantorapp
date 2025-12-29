<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Tandai alfa setiap hari pukul 23:59 WIB
        $schedule->command('attendance:mark-absences')
            ->dailyAt('23:59')
            ->timezone('Asia/Jakarta');

        // Rekap jam kerja bulanan (jalan harian untuk menjaga akumulasi)
        $schedule->command('work-recaps:generate')
            ->dailyAt('01:00')
            ->timezone('Asia/Jakarta');

        // Notifikasi approval pending (cek per lokasi)
        $schedule->command('notify:pending-approvals')
            ->everyMinute()
            ->timezone('Asia/Jakarta');

        // Pengingat jadwal masuk H-1 (cek per lokasi)
        $schedule->command('notify:shift-h1')
            ->everyMinute()
            ->timezone('Asia/Jakarta');

        // Ringkasan sisa cuti akhir bulan (cek per lokasi)
        $schedule->command('notify:leave-monthly-summary')
            ->everyMinute()
            ->timezone('Asia/Jakarta');

        // Pengingat awal bulan untuk pengajuan cuti (cek per lokasi)
        $schedule->command('notify:leave-monthly-reminder')
            ->everyMinute()
            ->timezone('Asia/Jakarta');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\GenerateShiftRotation::class,
        Commands\SendShiftReminders::class,
        Commands\SeedShiftTemplates::class,
        Commands\ShiftRosterHealthCheck::class,
        Commands\GenerateWorkRecaps::class,
        Commands\BackfillTaskSlots::class,
        Commands\NotifyPendingApprovals::class,
        Commands\NotifyShiftH1::class,
        Commands\NotifyLeaveMonthlySummary::class,
        Commands\NotifyLeaveMonthlyReminder::class,
    ];
}

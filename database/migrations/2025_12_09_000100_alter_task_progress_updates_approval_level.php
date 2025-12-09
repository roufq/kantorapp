<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ubah kolom enum menjadi string agar level baru (mis. manager) tidak terpotong
        DB::statement("ALTER TABLE task_progress_updates MODIFY approval_level VARCHAR(50) NOT NULL DEFAULT 'location_admin'");
    }

    public function down(): void
    {
        // Kembalikan ke enum semula jika diperlukan
        DB::statement("ALTER TABLE task_progress_updates MODIFY approval_level ENUM('none','location_admin','super_admin') NOT NULL DEFAULT 'location_admin'");
    }
};

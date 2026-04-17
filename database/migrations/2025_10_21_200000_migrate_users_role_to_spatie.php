<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Map legacy roles to Spatie role names
        $map = [
            'master' => 'Super Admin',
            'employee' => 'Employee',
        ];

        // Assign Spatie roles based on legacy users.role values (if column exists)
        if (Schema::hasColumn('users', 'role')) {
            // Ensure roles exist
            if (class_exists(\Spatie\Permission\Models\Role::class)) {
                foreach (array_values($map) as $spatieRole) {
                    \Spatie\Permission\Models\Role::firstOrCreate(['name' => $spatieRole]);
                }
            }

            // Assign roles to users
            $users = DB::table('users')->select('id', 'role')->get();
            foreach ($users as $u) {
                $legacy = $u->role;
                if ($legacy && isset($map[$legacy])) {
                    $roleName = $map[$legacy];
                    // Insert into model_has_roles if not present
                    $roleId = DB::table('roles')->where('name', $roleName)->value('id');
                    if ($roleId) {
                        $exists = DB::table('model_has_roles')
                            ->where('role_id', $roleId)
                            ->where('model_type', App\Models\User::class)
                            ->where('model_id', $u->id)
                            ->exists();
                        if (!$exists) {
                            DB::table('model_has_roles')->insert([
                                'role_id' => $roleId,
                                'model_type' => App\Models\User::class,
                                'model_id' => $u->id,
                            ]);
                        }
                    }
                }
            }

            // Drop legacy role column
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }

    public function down(): void
    {
        // Recreate legacy role column and set default values
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->enum('role', ['master', 'employee'])->default('employee')->after('email_verified_at');
            });
        }

        // Try to backfill from Spatie roles
        if (Schema::hasTable('model_has_roles') && Schema::hasTable('roles')) {
            $roleMap = [
                'Super Admin' => 'master',
                'Employee' => 'employee',
            ];
            $pairs = DB::table('model_has_roles as m')
                ->join('roles as r', 'r.id', '=', 'm.role_id')
                ->where('m.model_type', App\Models\User::class)
                ->select('m.model_id as user_id', 'r.name as role_name')
                ->get();
            foreach ($pairs as $p) {
                $legacy = $roleMap[$p->role_name] ?? null;
                if ($legacy) {
                    DB::table('users')->where('id', $p->user_id)->update(['role' => $legacy]);
                }
            }
        }
    }
};


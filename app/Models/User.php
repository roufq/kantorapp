<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'employee_id',
        'karyawan_id',
        'location_id',
        'phone_number',
        'profile_photo_path',
        'two_factor_secret',
        'two_factor_method',
        'two_factor_enabled',
        'two_factor_backup_codes',
        'two_factor_confirmed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_secret' => 'encrypted',
        ];
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public function receivedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function shiftAssignments()
    {
        return $this->hasMany(\App\Models\ShiftAssignment::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Employee::class, 'karyawan_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Check if user has 2FA enabled
     */
    public function hasTwoFactorEnabled()
    {
        return (bool) $this->two_factor_enabled;
    }

    /**
     * Generate backup codes for 2FA
     */
    public function generateBackupCodes()
    {
        $codes = [];
        for ($i = 0; $i < 10; $i++) {
            $codes[] = strtoupper(substr(md5(microtime() . rand()), 0, 8));
        }
        $this->two_factor_backup_codes = json_encode($codes);
        $this->save();
        return $codes;
    }

    /**
     * Verify backup code
     */
    public function verifyBackupCode($code)
    {
        if (!$this->two_factor_backup_codes) {
            return false;
        }

        $codes = json_decode($this->two_factor_backup_codes, true);
        $index = array_search($code, $codes);

        if ($index !== false) {
            unset($codes[$index]);
            $this->two_factor_backup_codes = json_encode(array_values($codes));
            $this->save();
            return true;
        }

        return false;
    }
}

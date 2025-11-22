<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'reporter_id',
        'location_id',
        'assigned_admin_id',
        'title',
        'description',
        'status',
        'finalized_at',
    ];

    protected $casts = [
        'finalized_at' => 'datetime',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }

    public function attachments()
    {
        return $this->hasMany(ReportAttachment::class);
    }

    public function approvals()
    {
        return $this->hasMany(ReportApproval::class);
    }

    public function currentPendingApproval()
    {
        return $this->approvals()->where('status', 'pending')->orderBy('step_order')->first();
    }

    public function recalcStatus(): void
    {
        $pending = $this->approvals()->where('status', 'pending')->exists();
        $rejected = $this->approvals()->where('status', 'rejected')->exists();
        $approved = !$pending && !$rejected;

        if ($rejected) {
            $this->status = 'rejected';
            $this->finalized_at = now();
        } elseif ($approved) {
            $this->status = 'approved';
            $this->finalized_at = now();
        } else {
            $this->status = 'pending';
            $this->finalized_at = null;
        }

        $this->save();
    }

    public static function generateTicketNumber(): string
    {
        $datePart = now('Asia/Jakarta')->format('Ymd');
        do {
            $ticket = 'RPT-' . $datePart . '-' . strtoupper(Str::random(5));
        } while (self::where('ticket_number', $ticket)->exists());

        return $ticket;
    }
}

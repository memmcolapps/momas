<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoggedIssue extends Model
{
    use HasFactory;

    protected $table = 'logged_issues';

    protected $fillable = [
        'ticket_id',
        'estate_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'meter_no',
        'house_no',
        'category',
        'priority',
        'title',
        'description',
        'attachment',
        'status',
        'resolution_notes',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'status' => 'integer',
        'resolved_at' => 'datetime',
    ];

    const STATUS_OPEN = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_RESOLVED = 2;
    const STATUS_CLOSED = 3;

    public function estate()
    {
        return $this->belongsTo(Estate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function meter()
    {
        return $this->belongsTo(Meter::class, 'meter_no', 'meter_no');
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            0 => 'Open',
            1 => 'In Progress',
            2 => 'Resolved',
            3 => 'Closed',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            0 => 'warning',
            1 => 'info',
            2 => 'success',
            3 => 'secondary',
            default => 'dark',
        };
    }

    public function getPriorityBadgeAttribute()
    {
        return match (strtolower($this->priority)) {
            'urgent' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
            default => 'primary',
        };
    }

    public static function generateTicketId()
    {
        $prefix = 'ISS-' . date('Ymd');
        $lastIssue = self::where('ticket_id', 'like', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastIssue) {
            $parts = explode('-', $lastIssue->ticket_id);
            $lastNum = (int) end($parts);
            $nextNum = str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNum = '0001';
        }

        return $prefix . '-' . $nextNum;
    }
}

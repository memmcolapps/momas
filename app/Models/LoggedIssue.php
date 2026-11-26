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

    protected $appends = [
        'status_label',
        'status_badge',
        'priority_badge',
        'attachment_url',
        'ticket_id_number',
        'first_name',
        'last_name',
    ];

    const STATUS_OPEN = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_RESOLVED = 2;
    const STATUS_CLOSED = 3;

    public const CATEGORIES = [
        'Power / Meter',
        'Token Generation',
        'Billing / Tariff',
        'Water / Utility',
        'Security / Gate',
        'Facility Maintenance',
        'General',
    ];

    public const PRIORITIES = [
        'low',
        'medium',
        'high',
        'urgent',
    ];

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

    public function getAttachmentUrlAttribute()
    {
        if (!$this->attachment) {
            return null;
        }

        if (str_starts_with($this->attachment, 'http://') || str_starts_with($this->attachment, 'https://')) {
            return $this->attachment;
        }

        return url($this->attachment);
    }

    public function getTicketIdNumberAttribute(): string
    {
        if (preg_match('/(\d{5,8})/', $this->ticket_id, $matches)) {
            return $matches[1];
        }
        return str_pad($this->id, 6, '133', STR_PAD_LEFT);
    }

    public function getFirstNameAttribute(): string
    {
        if ($this->user && $this->user->first_name) {
            return $this->user->first_name;
        }
        $parts = explode(' ', trim($this->customer_name ?? ''));
        return $parts[0] ?? '';
    }

    public function getLastNameAttribute(): string
    {
        if ($this->user && $this->user->last_name) {
            return $this->user->last_name;
        }
        $parts = explode(' ', trim($this->customer_name ?? ''));
        array_shift($parts);
        return implode(' ', $parts);
    }

    public function getConversationAttribute(): array
    {
        $messages = [];

        // First message is the initial resident ticket description
        $messages[] = [
            'sender'    => $this->customer_name ?: 'Resident',
            'role'      => 'customer',
            'ticket_id' => $this->ticket_id_number,
            'message'   => $this->description,
            'time'      => $this->created_at ? $this->created_at->format('h:i A') : '',
            'created_at'=> $this->created_at ? $this->created_at->toDateTimeString() : '',
            'attachment'=> $this->attachment,
        ];

        // Decoded replies from resolution_notes
        if ($this->resolution_notes) {
            $decoded = json_decode($this->resolution_notes, true);
            if (is_array($decoded)) {
                foreach ($decoded as $reply) {
                    $messages[] = [
                        'sender'    => $reply['sender'] ?? 'Admin',
                        'role'      => $reply['role'] ?? 'admin',
                        'ticket_id' => $reply['ticket_id'] ?? $this->ticket_id_number,
                        'message'   => $reply['message'] ?? '',
                        'time'      => !empty($reply['time']) ? date('h:i A', strtotime($reply['time'])) : '',
                        'created_at'=> $reply['time'] ?? '',
                        'attachment'=> $reply['attachment'] ?? null,
                    ];
                }
            } else {
                $messages[] = [
                    'sender'    => $this->resolver ? ($this->resolver->first_name . ' ' . $this->resolver->last_name) : 'Admin',
                    'role'      => 'admin',
                    'ticket_id' => $this->ticket_id_number,
                    'message'   => $this->resolution_notes,
                    'time'      => $this->resolved_at ? $this->resolved_at->format('h:i A') : ($this->updated_at ? $this->updated_at->format('h:i A') : ''),
                    'created_at'=> $this->resolved_at ? $this->resolved_at->toDateTimeString() : '',
                    'attachment'=> null,
                ];
            }
        }

        return $messages;
    }

    public function addReply(string $message, string $sender = 'Admin', string $role = 'admin', ?string $attachment = null): void
    {
        $replies = [];
        if ($this->resolution_notes) {
            $decoded = json_decode($this->resolution_notes, true);
            if (is_array($decoded)) {
                $replies = $decoded;
            } else {
                $replies[] = [
                    'sender'    => 'Admin',
                    'role'      => 'admin',
                    'ticket_id' => $this->ticket_id_number,
                    'message'   => $this->resolution_notes,
                    'time'      => $this->resolved_at ? $this->resolved_at->toDateTimeString() : now()->toDateTimeString(),
                    'attachment'=> null,
                ];
            }
        }

        $replies[] = [
            'sender'    => $sender,
            'role'      => $role,
            'ticket_id' => $this->ticket_id_number,
            'message'   => $message,
            'time'      => now()->toDateTimeString(),
            'attachment'=> $attachment,
        ];

        $this->resolution_notes = json_encode($replies);
        $this->save();
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'inquiry_id',
        'artist_id',
        'user_id',
        'total_amount',
        'estimated_duration',
        'notes',
        'validity_date',
        'status', // pending, accepted, declined, revision_requested, expired
        'revision_notes',
        'appointment_id',
        'queue_entry_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'validity_date' => 'date',
    ];

    public function inquiry()
    {
        return $this->belongsTo(Inquiry::class);
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function queueEntry()
    {
        return $this->belongsTo(QueueEntry::class);
    }

    public function isExpired()
    {
        return $this->validity_date && $this->validity_date->isPast() && $this->status === 'pending';
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->isExpired()) {
            return '<span class="badge bg-secondary">Expired</span>';
        }

        return match ($this->status) {
            'pending' => '<span class="badge bg-warning text-dark">Awaiting Client</span>',
            'accepted' => '<span class="badge bg-success">Accepted</span>',
            'declined' => '<span class="badge bg-danger">Declined</span>',
            'revision_requested' => '<span class="badge bg-info text-dark">Revision Requested</span>',
            'expired' => '<span class="badge bg-secondary">Expired</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}

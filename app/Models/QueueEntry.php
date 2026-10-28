<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QueueEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id',
        'service_id',
        'user_id',
        'queue_number',
        'client_name',
        'phone_number',
        'queue_date',
        'status',
        'estimated_wait_minutes',
        'called_at',
        'service_started_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'queue_date' => 'date',
        'called_at' => 'datetime',
        'service_started_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_wait_minutes' => 'integer',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeToday($query)
    {
        return $query->where('queue_date', now()->toDateString());
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['waiting', 'in_service']);
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Get queue position (0 = currently in chair, 1 = up next, 2 = 2nd in line, etc.)
     */
    public function getPositionAttribute(): int
    {
        if ($this->status === 'in_service') {
            return 0;
        }

        if ($this->status !== 'waiting') {
            return -1;
        }

        $activeAhead = self::where('artist_id', $this->artist_id)
            ->where('queue_date', $this->queue_date)
            ->where(function ($q) {
                $q->where('status', 'in_service')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'waiting')
                          ->where('id', '<', $this->id);
                  });
            })
            ->count();

        return $activeAhead;
    }

    /**
     * Compute estimated wait time in minutes for this ticket
     */
    public function computeEstimatedWait(): int
    {
        if ($this->status === 'in_service') {
            return 0;
        }

        $ticketsAhead = self::with('service')
            ->where('artist_id', $this->artist_id)
            ->where('queue_date', $this->queue_date)
            ->where(function ($q) {
                $q->where('status', 'in_service')
                  ->orWhere(function ($sub) {
                      $sub->where('status', 'waiting')
                          ->where('id', '<', $this->id);
                  });
            })
            ->get();

        $totalMinutes = 0;
        foreach ($ticketsAhead as $t) {
            $duration = $t->service ? $t->service->duration_minutes : 30;
            if ($t->status === 'in_service' && $t->service_started_at) {
                $elapsed = (int) $t->service_started_at->diffInMinutes(now());
                $remaining = max(0, $duration - $elapsed);
                $totalMinutes += $remaining;
            } else {
                $totalMinutes += $duration;
            }
        }

        return $totalMinutes;
    }
}

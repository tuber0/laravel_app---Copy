<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'specialty',
        'specialties',
        'category',
        'rating',
        'bio',
        'years_of_experience',
        'skills',
        'certifications',
        'languages',
        'starting_rate',
        'social_links',
        'preferred_styles',
        'image_url',
        'profile_photo',
        'portfolio_images',
        'live_status',
        'working_hours',
    ];

    protected $casts = [
        'specialty' => 'array',
        'specialties' => 'array',
        'skills' => 'array',
        'certifications' => 'array',
        'languages' => 'array',
        'social_links' => 'array',
        'preferred_styles' => 'array',
        'portfolio_images' => 'array',
        'working_hours' => 'array',
        'starting_rate' => 'decimal:2',
        'rating' => 'decimal:2',
        'years_of_experience' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function portfolioItems()
    {
        return $this->hasMany(PortfolioItem::class)->orderBy('sort_order')->orderBy('id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function scheduleRequests()
    {
        return $this->hasMany(ScheduleRequest::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function queueEntries()
    {
        return $this->hasMany(QueueEntry::class);
    }

    public function activeQueue()
    {
        return $this->hasMany(QueueEntry::class)
            ->where('queue_date', now()->toDateString())
            ->whereIn('status', ['waiting', 'in_service'])
            ->orderBy('id');
    }

    public function getCurrentQueueLengthAttribute(): int
    {
        return $this->activeQueue()->count();
    }

    public function getEstimatedWaitMinutesAttribute(): int
    {
        $active = $this->activeQueue()->with('service')->get();
        $totalMinutes = 0;
        foreach ($active as $ticket) {
            $duration = $ticket->service ? $ticket->service->duration_minutes : 30;
            if ($ticket->status === 'in_service' && $ticket->service_started_at) {
                $elapsed = (int) $ticket->service_started_at->diffInMinutes(now());
                $remaining = max(0, $duration - $elapsed);
                $totalMinutes += $remaining;
            } else {
                $totalMinutes += $duration;
            }
        }
        return $totalMinutes;
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class)->latest();
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class)->latest();
    }
}


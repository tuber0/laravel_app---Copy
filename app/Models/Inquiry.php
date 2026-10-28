<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'artist_id',
        'service_type',
        'description',
        'reference_images',
        'preferred_date',
        'budget',
        'status', // pending, quoted, accepted, declined, expired
    ];

    protected $casts = [
        'reference_images' => 'array',
        'preferred_date' => 'date',
        'budget' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function messages()
    {
        return $this->hasMany(InquiryMessage::class)->orderBy('created_at', 'asc');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class)->orderBy('created_at', 'desc');
    }

    public function latestQuotation()
    {
        return $this->hasOne(Quotation::class)->latestOfMany();
    }

    public function scopeForCustomer($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForArtist($query, $artistId)
    {
        return $query->where('artist_id', $artistId);
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'pending' => '<span class="badge bg-warning text-dark">Pending Review</span>',
            'quoted' => '<span class="badge bg-info text-dark">Quotation Sent</span>',
            'accepted' => '<span class="badge bg-success">Accepted</span>',
            'declined' => '<span class="badge bg-danger">Declined</span>',
            'expired' => '<span class="badge bg-secondary">Expired</span>',
            default => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getReferenceImageUrlsAttribute()
    {
        if (empty($this->reference_images)) {
            return [];
        }

        $raw = $this->reference_images;
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : [$raw];
        }

        if (!is_array($raw)) {
            return [];
        }

        return array_values(array_filter(array_map(function ($path) {
            if (empty($path)) return null;
            if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
                return $path;
            }
            $clean = ltrim(preg_replace('#^/?storage/#', '', $path), '/');
            return asset('storage/' . $clean);
        }, $raw)));
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'category', 'description', 'price', 'duration'];

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function queueEntries()
    {
        return $this->hasMany(QueueEntry::class);
    }

    public function getDurationMinutesAttribute(): int
    {
        if (preg_match('/(\d+)/', (string)$this->duration, $matches)) {
            return (int)$matches[1];
        }
        return 30; // default 30 mins
    }

    public static function getAllCategories(): \Illuminate\Support\Collection
    {
        $standards = collect(['Barbering', 'Hair Styling', 'Braiding', 'Tattoo', 'Piercing', 'Others']);
        $serviceCats = static::pluck('category')->filter()->map('trim');
        $artistCats = \App\Models\Artist::pluck('category')->filter()->map('trim');
        return $standards->merge($serviceCats)->merge($artistCats)->unique()->filter()->sort()->values();
    }
}


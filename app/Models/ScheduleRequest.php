<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id',
        'date',
        'time',
        'type',
        'reason',
        'status'
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }
}

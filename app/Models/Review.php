<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
class Review extends Model
{
    use HasFactory;
 
    protected $fillable = [
        'user_id',
        'artist_id',
        'appointment_id',
        'rating',
        'comment',
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
 
    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }
 
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'phone',
        'profile_picture',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin() { return $this->role === 'admin'; }
    public function isStaff() { return $this->role === 'staff'; }
    public function isCustomer() { return $this->role === 'customer'; }

    public function artist()
    {
        return $this->hasOne(Artist::class);
    }

    public function inquiries()
    {
        return $this->hasMany(Inquiry::class);
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class);
    }

    public function inAppNotifications()
    {
        return $this->hasMany(InAppNotification::class)->latest();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InAppNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type', // info, success, warning, quote
        'title',
        'message',
        'link',
        'message_id',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inquiryMessage()
    {
        return $this->belongsTo(InquiryMessage::class, 'message_id');
    }

    public static function send($userId, $title, $message, $link = null, $type = 'info', $messageId = null)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'message_id' => $messageId,
            'is_read' => false,
        ]);
    }
}

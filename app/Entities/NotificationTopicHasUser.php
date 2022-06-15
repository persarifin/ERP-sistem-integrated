<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTopicHasUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'notification_topic_id',
        'user_id'
    ];
}

<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTopic extends Model
{
    use HasFactory;
    protected $fillable = [
        'notification_topic'
    ];
}

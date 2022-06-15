<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationActivation extends Model
{
    use HasFactory;
    protected $fillable = [
        'activation',
        'user_id'
    ];
}

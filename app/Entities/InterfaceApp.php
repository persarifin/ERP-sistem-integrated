<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterfaceApp extends Model
{
    use HasFactory;
    protected $fillable = [
        'interface_name'
    ];

    protected $table = 'interfaces';
}
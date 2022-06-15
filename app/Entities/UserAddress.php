<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAddress extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'user_id',
        'type',
        'name',
        'address',
        'subdistrict',
        'city',
        'province',
        'country',
        'postal_code',
        'is_primary'
    ];

    public function user()
  {
    return $this->belongsTo(User::class);
  }
}

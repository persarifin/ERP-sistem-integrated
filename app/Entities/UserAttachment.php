<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAttachment extends Model 
{
  use SoftDeletes;
  protected $fillable = [
    'user_id',
    'attachment_type',
    'file_name',
    'file_location'
  ];

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
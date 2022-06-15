<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentComment extends Model
{
  use SoftDeletes;
    protected $fillable = [
        'comment',
        'content_id',
        'user_id',
        'date'
    ];
    
    public function content()
    {
      return $this->belongsTo(Content::class);
    }
}

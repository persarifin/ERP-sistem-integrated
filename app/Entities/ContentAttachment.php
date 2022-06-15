<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentAttachment extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'attachment_type',
        'file_name',
        'file_location',
        'content_id',
        'company_id'
    ];
    public function company()
    {
      return $this->belongsTo(Company::class, 'company_id');
    }
    public function content()
    {
        return $this->belongsTo(Content::class);
    }
}

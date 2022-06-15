<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_name',
        'company_id'
    ];
    public function company()
    {
      return $this->belongsTo(Company::class, 'company_id');
    }
    public function content_category_attachment()
    {
      return $this->hasOne(ContentCategoryAttachment::class);
    }
}

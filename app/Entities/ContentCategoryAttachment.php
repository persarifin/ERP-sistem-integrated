<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentCategoryAttachment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'category_id',
        'company_id',
        'file_name',
        'file_location'
    ];
    public function content_category()
    {
      return $this->belongsTo(ContentCategory::class);
    }
    public function company()
  {
    return $this->belongsTo(Company::class, 'company_id');
  }
}
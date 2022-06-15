<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyAttachment extends Model 
{
  use SoftDeletes;
  protected $fillable = [
    'company_id',
    'attachment_type',
    'file_name',
    'file_location'
  ];

  public function company()
  {
    return $this->belongsTo(Company::class, 'company_id');
  }
}
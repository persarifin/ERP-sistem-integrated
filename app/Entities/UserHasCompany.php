<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class UserHasCompany extends Model 
{
  protected $fillable = [
    'user_id',
    'company_id'
  ];
  public function company()
  {
    return $this->belongsTo(Company::class,'company_id');
  }
}
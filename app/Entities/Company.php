<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model 
{
  use SoftDeletes;
  protected $fillable = [
    'business_name',
    'legal_name',
    'tax_number',
    'email',
    'email_verified_at',
    'phone',
    'phone_verified_at',
    'address',
    'subdistrict',
    'city',
    'province',
    'postal_code',
    'country',
    'bio',
    'tagline',
    'sub_tagline',
    'vision',
    'mission',
    'work_culture',
    'working_space'
  ];

  protected $appends = ['user_enterprise'];

  public function getUserEnterpriseAttribute()
  {
      return User::where('company_id', $this->id)->whereHas('roles', function ($q) {
        $q->where('custom_name', 'enterprise');
      })->first();
  }


  public function users()
  {
    return $this->hasMany(User::class);
  }
  
  public function company_attachments()
  {
    return $this->hasMany(CompanyAttachment::class, 'company_id');
  }
  public function company_logo()
  {
    return $this->hasOne(CompanyAttachment::class, 'company_id')->where('attachment_type', 'COMPANY_LOGO');
  }
  
  public function product_category()
  {
    return $this->hasMany(ProductCategory::class, 'company_id');
  }
  
  public function billing_invoices()
  {
    return $this->hasMany(BillingInvoice::class, 'company_id');
  }

  public function not_approved_billing_invoice()
  {
    return $this->hasOne(BillingInvoice::class, 'company_id')->where('billing_invoices.is_approved', '=', false)->orderByDesc('date');
  }
  public function user_has_company()
  {
    return $this->belongsToMany(User::class, 'user_has_companies', 'company_id', 'user_id');
  }
}
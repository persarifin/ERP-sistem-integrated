<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class BillingCounter extends Model
{
    protected $fillable = [
        'counter_name',
        'amount',
        'date',
        'company_id'
    ];
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

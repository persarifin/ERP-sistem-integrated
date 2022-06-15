<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

class BillingInvoice extends Model
{
    protected $fillable = [
        'invoice_name',
        'amount',
        'date',
        'is_approved',
        'status',
        'company_id'
    ];
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransactionAttachment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'transaction_id',
        'company_id',
        'file_name',
        'file_location'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function payment_transaction()
    {
        return $this->belongsTo(PaymentTransaction::class);
    }
}

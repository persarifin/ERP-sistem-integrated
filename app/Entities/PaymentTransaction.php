<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentTransaction extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'amount',
        'transaction_date',
        'company_wallet_id',
        'user_id',
        'company_id',
        'submission_id'
    ];

    public function company_wallet()
    {
        return $this->belongsTo(CompanyWallet::class, 'company_wallet_id');
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class,'submission_id');
    }
    public function payment_reconciliation()
    {
        return $this->hasOne(PaymentReconciliation::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id')->with(['company_logo']);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function payment_transaction_attachment()
    {
        return $this->hasMany(PaymentTransactionAttachment::class,'transaction_id');
    }

    
}

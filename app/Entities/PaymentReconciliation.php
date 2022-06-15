<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentReconciliation extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'amount',
        'user_id',
        'company_id',
        'from_wallet_id',
        'to_wallet_id'
    ];

    public function from_wallet()
    {
        return $this->belongsTo(CompanyWallet::class,'from_wallet_id');
    }
    public function to_wallet()
    {
        return $this->belongsTo(CompanyWallet::class,'to_wallet_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function user()
    {
        return $this->belongsTo(Company::class);
    }
}

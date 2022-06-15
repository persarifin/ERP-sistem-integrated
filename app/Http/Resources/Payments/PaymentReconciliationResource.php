<?php

namespace App\Http\Resources\Payments;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\CompanyWalletResource;
use App\Http\Resources\UserResource;

class PaymentReconciliationResource extends BaseResource
{
    protected $availableRelations = ['from_wallet','to_wallet','company','user'];
    protected $resourceType = 'payment_reconciliation';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'amount' => $this->amount,
            'from_wallet_id' => $this->from_wallet_id,
            'to_wallet_id' => $this->to_wallet_id,
            'company_id' => $this->company_id,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);
    }
    public function getCompanyRelation()
    {
        return new CompanyResource($this->company);
    }
    public function getFromWalletRelation()
    {
        return new CompanyWalletResource($this->from_wallet);
    }
    public function getToWalletRelation()
    {
        return new CompanyWalletResource($this->to_wallet);
    }
    public function getUserRelation()
    {
        return new UserResource($this->user);
    }
}
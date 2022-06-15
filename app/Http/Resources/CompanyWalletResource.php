<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class CompanyWalletResource extends BaseResource
{

  protected $availableRelations = ['company'];
  protected $resourceType = 'company_wallet';
  
  public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'company_id'    => $this->company_id,
            'wallet_name'   => $this->wallet_name,
            'amount_income' => $this->amount_income,
            'amount_expense'=> $this->amount_expense,
            'margin'        => $this->margin,
            'balance'       => $this->balance,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ]);
    }

  public function getCompanyRelation()
  {
    return new CompanyResource($this->company);
  }
}

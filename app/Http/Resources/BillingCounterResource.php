<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class BillingCounterResource extends BaseResource
{
    protected $availableRelations = ['company'];
    protected $resourceType = "billing_counter";
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id'            => $this->getIdentifier(),
            'counter_name'  => $this->counter_name,
            'amount'        => $this->amount,
            'date'          => $this->date,
            'company_id'    => $this->company_id,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ]);
    }
    public function getCompanyRelation()
    {
      return new CompanyResource($this->company);
    }
}

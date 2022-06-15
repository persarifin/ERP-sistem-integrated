<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class BillingInvoiceResource extends BaseResource
{
    protected $availableRelations = ['company'];
    protected $resourceType = "billing_invoice";
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id'            => $this->getIdentifier(),
            'invoice_name'  => $this->invoice_name,
            'amount'        => $this->amount,
            'date'          => $this->date,
            'is_approved'   => $this->is_approved,
            'status'        => $this->status,
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

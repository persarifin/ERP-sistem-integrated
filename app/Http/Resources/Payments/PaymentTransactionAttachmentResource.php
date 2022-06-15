<?php

namespace App\Http\Resources\Payments;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;

class PaymentTransactionAttachmentResource extends BaseResource
{
    protected $availableRelations = ['company'];
    protected $resourceType = 'payment_transaction_attachment';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'company_id' => $this->company_id,
            'transaction_id' => $this->transaction_id,
            'file_name' => $this->file_name,
            'file_location' => $this->file_location,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);
    }
    public function getCompanyRelation()
    {
        return new CompanyResource($this->company);
    }
}
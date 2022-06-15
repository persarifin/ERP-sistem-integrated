<?php

namespace App\Http\Resources\Items;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;

class ItemAttachmentResource extends BaseResource
{
    protected $availableRelations = ['company'];
    protected $resourceType = 'item_attachment';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id'            => $this->getIdentifier(),
            'company_id'    => $this->company_id,
            'item_id'       => $this->item_id,
            'file_name'     => $this->file_name,
            'file_location' => $this->file_location,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ]);
    }
    public function getCompanyRelation()
    {
        return new CompanyResource($this->company);
    }
}
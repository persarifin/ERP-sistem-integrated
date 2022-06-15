<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\Products\ProductResource;

class ProductAttachmentResource extends BaseResource
{
    protected $availableRelations = ['product','company'];
    protected $resourceType = 'product_attachment';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id'              => $this->getIdentifier(),
            'attachment_type' => $this->attachment_type,
            'company_id'      => $this->company_id,
            'product_id'      => $this->product_id,
            'file_name'       => $this->file_name,
            'file_location'   => $this->file_location,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at
        ]);
    }
    public function getCompanyRelation()
    {
        return new CompanyResource($this->company);
    }
    public function getProductRelation()
    {
        return ProductResource::collection($this->product);
    }
}
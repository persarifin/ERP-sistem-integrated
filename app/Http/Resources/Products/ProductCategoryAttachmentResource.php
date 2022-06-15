<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\BaseResource;
use App\Http\Resources\Products\ProductCategoryResource;
use App\Http\Resources\CompanyResource;

class ProductCategoryAttachmentResource extends BaseResource
{
    protected $availableRelations = ['product_category','company'];
    protected $resourceType = 'product_category_attachment';
    
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
    public function getProductCategoryRelation()
    {
        return ProductCategoryResource::collection($this->product_category);
    }
}
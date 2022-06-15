<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\Products\ProductResource;
use App\Http\Resources\Products\ProductCategoryAttachmentResource;

class ProductCategoryResource extends BaseResource
{
    protected $availableRelations = ['company','product','product_category_attachment'];
    protected $resourceType = 'product_category';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id'                => $this->getIdentifier(),
            'category_name'     => $this->category_name,
            'product_category_attachment' => $this->product_category_attachment,
            'description'       => $this->description,
            'company_id'        => $this->company_id,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ]);
    }
    public function getCompanyRelation()
    {
        return new CompanyResource($this->company);
    }
    public function getProductCategoryAttachmentRelation()
    {
        return ProductCategoryAttachmentResource::collection($this->product_category_attachment);
    }
    public function getProductRelation()
    {
        return ProductResource::collection($this->product);
    }
}
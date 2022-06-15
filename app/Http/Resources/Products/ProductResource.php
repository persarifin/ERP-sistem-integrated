<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\Products\ProductVisibilityResource;
use App\Http\Resources\Products\ProductCategoryResource;
use App\Http\Resources\Products\ProductAttachmentResource;

class ProductResource extends BaseResource
{
    protected $availableRelations = [
        'company',
        'product_category',
        'product_visibility',
        'product_attachment',
        'product_schedule'
    ];
    protected $resourceType = 'product';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id'                => $this->getIdentifier(),
            'product_name'      => $this->product_name,
            'product_type'      => $this->product_type,
            'description'       => $this->description,
            'stock'             => $this->stock,
            'min_stock'         => $this->min_stock,
            'already_sold'      => $this->already_sold,
            'buying_price'      => $this->buying_price,
            'selling_price'     => $this->selling_price,
            'status'            => $this->status,
            'unit'              => $this->unit,
            'company_id'        => $this->company_id,
            'category_id'       => $this->category_id,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ]);
    }
    public function getCompanyRelation()
    {
        return new CompanyResource($this->company);
    }
    public function getProductCategoryRelation()
    {
        return new ProductCategoryResource($this->product_category);
    }
    public function getProductVisibilityRelation()
    {
        return ProductVisibilityResource::collection($this->product_visibility);
    }
    //has relation include
    public function getProductAttachmentRelation()
    {
      return ProductAttachmentResource::collection($this->product_attachment);
    }
    public function getProductScheduleRelation()
    {
        return ProductScheduleResource::collection($this->product_schedule);
    }
    
}
<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\Products\ProductResource;
use App\Http\Resources\Items\ItemResource;

class ProductScheduleResource extends BaseResource
{
    protected $availableRelations = ['company','item','product'];
    protected $resourceType = 'product_schedule';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'start' => $this->start,
            'finish' => $this->finish,
            'item_id' => $this->item_id,
            'product_id'=> $this->product_id,
            'company_id' => $this->company_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);
    }
    public function getCompanyRelation()
    {
        return new CompanyResource($this->company);
    }
    public function getProductRelation()
    {
        return new ProductResource($this->product);
    }
    public function getItemRelation()
    {
        return new ItemResource($this->item);
    }
}
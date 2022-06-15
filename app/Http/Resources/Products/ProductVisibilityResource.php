<?php

namespace App\Http\Resources\Products;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\InterfaceApps\IterfaceResource;
use App\Http\Resources\Products\ProductResource;

class ProductVisibilityResource extends BaseResource
{
    protected $availableRelations = ['company','interface','product'];
    protected $resourceType = 'product_visibility';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'interface_id'=> $this->interface_id,
            'product_id'=> $this->product_id,
            'interface_name' => isset($this->interface) ? $this->interface->interface_name : '-',
            'company_id' => $this->company_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);
    }
    public function getCompanyRelation()
    {
        return new CompanyResource($this->company);
    }
    public function getInterfaceRelation()
    {
        return new InterfaceResource($this->interface);
    }
    public function getProductRelation()
    {
        return new ProductResource($this->product);
    }
}
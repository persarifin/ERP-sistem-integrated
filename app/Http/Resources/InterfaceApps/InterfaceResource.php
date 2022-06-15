<?php

namespace App\Http\Resources\InterfaceApps;

use App\Http\Resources\BaseResource;

class InterfaceResource extends BaseResource
{
    protected $availableRelations = [];
    protected $resourceType = 'interface';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id'             => $this->getIdentifier(),
            'interface_name' => $this->interface_name,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at
        ]);
    }
}
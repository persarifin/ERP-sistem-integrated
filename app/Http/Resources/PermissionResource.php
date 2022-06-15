<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class PermissionResource extends BaseResource
{
    protected $resourceType = "permission";
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'name' => $this->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);
    }
}

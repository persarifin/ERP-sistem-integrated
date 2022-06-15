<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class RoleResource extends BaseResource
{
    protected $availableRelations = ['permissions'];
    protected $resourceType = "role";
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'name' => $this->name,
            'custom_name' => $this->custom_name,
            'company_id' => $this->company_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);
    }

    public function getPermissionsRelation(){
      return PermissionResource::collection($this->permissions);
    }
}

<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class UserHasCompanyResource extends BaseResource
{

  protected $availableRelations = ['user'];
  protected $resourceType = 'user_has_companies';
  
  public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);
    }

  public function getUserRelation()
  {
    return new UserResource($this->user);
  }
}

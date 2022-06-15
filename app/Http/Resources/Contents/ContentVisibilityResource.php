<?php

namespace App\Http\Resources\Contents;

use App\Http\Resources\BaseResource;
use App\Http\Resources\InterfaceApps\InterfaceResource;
use App\Http\Resources\CompanyResource;

class ContentVisibilityResource extends BaseResource
{
    protected $availableRelations = ['company','interface'];
  protected $resourceType = 'content_visibility';
  
  public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'company_id' => $this->company_id,
            'content_id' => $this->content_id,
            'interface_id' => $this->interface_id,
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
}

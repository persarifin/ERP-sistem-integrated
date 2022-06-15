<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class CompanyAttachmentResource extends BaseResource
{

  protected $availableRelations = ['company'];
  protected $resourceType = 'company_attachment';
  
  public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'company_id' => $this->company_id,
            'attachment_type' => $this->attachment_type,
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
}

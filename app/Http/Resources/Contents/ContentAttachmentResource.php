<?php

namespace App\Http\Resources\Contents;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;

class ContentAttachmentResource extends BaseResource
{

  protected $availableRelations = ['company'];
  protected $resourceType = 'content_attachment';
  
  public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'company_id' => $this->company_id,
            'content_id' => $this->content_id,
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

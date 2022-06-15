<?php

namespace App\Http\Resources\Contents;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;

class ContentCategoryAttachmentResource extends BaseResource
{

  protected $availableRelations = ['company'];
  protected $resourceType = 'content_category_attachment';
  
  public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'company_id' => $this->company_id,
            'category_id' => $this->category_id,
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

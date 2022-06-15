<?php

namespace App\Http\Resources\Contents;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\Contents\ContentCategoryAttachmentResource;

class ContentCategoryResource extends BaseResource
{

  protected $availableRelations = ['company','content_category_attachment'];
  protected $resourceType = 'content_category';
  
  public function toArray($request)
    {
        return $this->transformResponse([
            'id'            => $this->getIdentifier(),
            'company_id'    => $this->company_id,
            'category_name' => $this->category_name,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ]);
    }

  public function getCompanyRelation()
  {
    return new CompanyResource($this->company);
  }
  public function getContentCategoryAttachmentRelation()
  {
    return ContentCategoryAttachmentResource::collection($this->content_category_attachment);
  }
}

<?php

namespace App\Http\Resources\Contents;

use App\Http\Resources\BaseResource;
use App\Http\Resources\Contents\ContentCategoryResource;
use App\Http\Resources\Contents\ContentCommentResource;
use App\Http\Resources\Contents\ContentAttachmentResource;
use App\Http\Resources\Contents\ContentVisibilityResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserResource;

class ContentResource extends BaseResource
{
    protected $availableRelations = [
      'company',
      'content_category',
      'user',
      'content_visibility',
      'content_attachment',
      'content_comment'
    ];
  protected $resourceType = 'content';
  
  public function toArray($request)
    {
        return $this->transformResponse([
            'id'            => $this->getIdentifier(),
            'company_id'    => $this->company_id,
            'category_id'   => $this->category_id,
            'user_id'       => $this->user_id,
            'content_name'  => $this->content_name,
            'date'          => $this->date,
            'status'        => $this->status,
            'content'       => $this->content,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ]);
    }

  public function getCompanyRelation()
  {
    return new CompanyResource($this->company);

  }
  public function getContentCategoryRelation()
  {
    return new ContentCategoryResource($this->content_category);
  }
  public function getUserRelation()
  {
    return new UserResource($this->user);
  }
  public function getContentVisibilityRelation()
  {
    return new ContentVisibilityResource($this->content_visibility);
  }
  //has relation include
  public function getContentCommentRelation()
  {
    return ContentCommentResource::collection($this->content_comment);
  }
  public function getContentAttachmentRelation()
  {
    return ContentAttachmentResource::collection($this->content_attachment);
  }

}

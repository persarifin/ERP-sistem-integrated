<?php

namespace App\Http\Resources\Contents;

use App\Http\Resources\BaseResource;
use App\Http\Resources\UserResource;

class ContentCommentResource extends BaseResource
{

  protected $availableRelations = ['user'];
  protected $resourceType = 'content_comment';
  
  public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'comment' => $this->comment,
            'content_id' => $this->content_id,
            'user_id' => $this->user_id,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);
    }

  public function getUserRelation()
  {
    return new UserResource($this->user);
  }
}

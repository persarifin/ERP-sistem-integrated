<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class UserAttachmentResource extends BaseResource
{

  protected $availableRelations = ['user'];
  protected $resourceType = 'user_attachment';
  
  public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'user_id' => $this->user_id,
            'type' => $this->type,
            'file_name' => $this->file_name,
            'file_location' => $this->file_location,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);
    }

  public function getUserRelation()
  {
    return new UserResource($this->user);
  }
}

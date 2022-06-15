<?php

namespace App\Http\Resources\Statuses;

use App\Http\Resources\BaseResource;

class ReadStatusResource extends BaseResource
{
    protected $availableRelations = ['user_read','submission'];
    protected $resourceType = 'read_status';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id'            => $this->getIdentifier(),
            'user_id'       => $this->user_id,
            'read_table'    => $this->read_table,
            'table_row_id'  => $this->table_row_id,
            'user'          => $this->user_read,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ]);
    }
    public function getUserReadRelation()
    {
        return new UserResource($this->user_read);
    }
    public function getSubmissionRelation()
    {
        return new SubmissionResource($this->submission);
    }
}
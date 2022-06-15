<?php

namespace App\Http\Resources\Submissions;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\UserResource;

class SubmissionCommentResource extends BaseResource
{
    protected $availableRelations = ['company', 'user'];
    protected $resourceType = 'submission_comment';

    public function toArray($request)
    {
        return $this->transformResponse([
            'id'            => $this->getIdentifier(),
            'comment'       => $this->comment,
            'date'          => $this->date,
            'user_id'       => $this->user_id,
            'submission_id' => $this->submission_id,
            'company_id'    => $this->company_id,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ]);
    } 

    public function getCompanyRelation()
    {
      return new CompanyResource($this->company);
    }

    public function getUserRelation(){
        return new UserResource($this->user);
    }
}

<?php

namespace App\Http\Resources\Submissions;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;

class SubmissionAttachmentResource extends BaseResource
{
    protected $availableRelations = ['submission', 'company'];
    protected $resourceType = 'submission_attachment';

    public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'file_location' => $this->file_location,
            'file_name' => $this->file_name,
            'submission_id' => $this->submission_id,
            'company_id' => $this->company_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);
    } 
    public function getCompanyRelation()
    {
      return new CompanyResource($this->company);
    }
    public function getSubmissionRelation()
    {
      return new SubmissionResource($this->submission);
    }
}

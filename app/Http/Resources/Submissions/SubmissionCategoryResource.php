<?php

namespace App\Http\Resources\Submissions;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;

class SubmissionCategoryResource extends BaseResource
{
    protected $availableRelations = ['company','submission'];
    protected $resourceType = 'submission_category';

    public function toArray($request)
    {
        return $this->transformResponse([
            'id'                => $this->getIdentifier(),
            'category_name'     => $this->category_name,
            'maximum'           => $this->maximum,
            'submission_type'   => $this->submission_type,
            'company_id'        => $this->company_id,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at
        ]);
    } 

    public function getCompanyRelation()
    {
      return new CompanyResource($this->company);
    }
    public function getSubmissionRelation()
    {
      return SubmissionResource::collection($this->submission);
    }
}

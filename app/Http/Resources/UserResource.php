<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class UserResource extends BaseResource
{
    protected $availableRelations = ['company', 'roles','user_attachment', 'user_has_company'];
    protected $resourceType = 'user';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id'              => $this->getIdentifier(),
            'full_name'       => $this->full_name,
            'email'           => $this->email,
            'username'        => $this->username,
            'activated'       => $this->activated,
            // 'company'         => $this->company,
            'phone'           => $this->phone,
            'is_online'       => $this->is_online,
            'role'            => $this->roles,
            'user_attachment' => $this->user_attachment,
            'company_id'      => $this->company_id,
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at
        ]);
    }

    public function getCompanyRelation()
    {
      return new CompanyResource($this->company);
    }

    public function getRolesRelation()
    {
      return RoleResource::collection($this->roles);
    }
    public function getUserAttachmentRelation()
    {
      return RoleResource::collection($this->user_attachment);
    }

    public function getUserHasCompanyRelation()
    {
      return CompanyResource::collection($this->user_has_company);
    }
}

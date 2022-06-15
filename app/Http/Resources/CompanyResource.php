<?php

namespace App\Http\Resources;

use App\Http\Resources\BaseResource;

class CompanyResource extends BaseResource
{
  protected $availableRelations = ['users', 'company_attachments', 'billing_invoices', 'not_approved_billing_invoice'];
  protected $resourceType = 'company';
  
  public function toArray($request)
    {
        return $this->transformResponse([
            'id' => $this->getIdentifier(),
            'business_name' => $this->business_name,
            'legal_name' => $this->legal_name,
            'tax_number' => $this->tax_number,
            'company_logo' => $this->company_logo,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'phone' => $this->phone,
            'phone_verified_at' => $this->phone_verified_at,
            'address' => $this->address,
            'subdistrict' => $this->subdistrict,
            'city' => $this->city,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'bio' => $this->bio,
            'tagline' => $this->tagline,
            'sub_tagline' => $this->sub_tagline,
            'vision' => $this->vision,
            'mission' => $this->mission,
            'work_culture' => $this->work_culture,
            'working_space'=> $this->working_space,
            'user_enterprise' => $this->user_enterprise,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);
    }
  
  public function getUsersRelation()
  {
    return UserResource::collection($this->users);
  }

  public function getCompanyAttachmentsRelation()
  {
    return CompanyAttachmentResource::collection($this->company_attachments);
  }

  public function getBillingInvoicesRelation()
  {
    return BillingInvoiceResource::collection($this->billing_invoices);
  }

  public function getNotApprovedBillingInvoiceRelation()
  {
    return new BillingInvoiceResource($this->not_approved_billing_invoice);
  }
}

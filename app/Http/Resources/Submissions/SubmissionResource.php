<?php

namespace App\Http\Resources\Submissions;

use App\Http\Resources\BaseResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\Items\ItemResource;
use App\Http\Resources\Submissions\SubmissionCategoryResource;
use App\Http\Resources\Payments\PaymentTransactionResource;
use App\Http\Resources\Submissions\SubmissionAttachmentResource;
use App\Http\Resources\Statuses\ReadStatusResource;

class SubmissionResource extends BaseResource
{
    protected $availableRelations = [
      'user',
      'submission_category',
      'company', 
      'read',
      'submission_attachment',
      'item',
      'payment_transaction'
    ];
    protected $resourceType = 'submission';

    public function toArray($request)
    {
        return $this->transformResponse([
            'id'                    => $this->getIdentifier(),
            'submission_name'       => $this->submission_name,
            'description'           => $this->description,
            'amount'                => $this->amount,
            'date'                  => $this->date,
            'due_date'              => $this->due_date,
            'status'                => $this->status,
            'fullfilment'           => $this->fullfilment, 
            'reference_doc_number'  => $this->reference_doc_number,
            'user_id'               => $this->user_id,
            'paid'                  => $this->paid,
            'submission_type'       => $this->submission_category->submission_type,
            'submission_attachment' => $this->submission_attachment,
            'read'                  => $this->read,
            'item'                  => $this->item,
            'customer'              => $this->customer,
            'company_id'            => $this->company_id,
            'partner_id'            => $this->partner_id,
            'category_id'           => $this->category_id,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at

        ]);
    } 

    public function getUserRelation()
    {
      return new UserResource($this->user);
    }
    public function getCompanyRelation()
    {
      return new CompanyResource($this->company);
    }
    public function getSubmissionCategoryRelation()
    {
      return new SubmissionCategoryResource($this->submission_category);
    }
    public function getReadRelation()
    {
      return ReadStatusResource::collection($this->read);
    }
    public function getSubmissionAttachmentRelation()
    {
      return SubmissionAttachmentResource::collection($this->submission_attachment);
    }
    public function getPaymentTransactionRelation()
    {
      return PaymentTransactionResource::collection($this->payment_transaction);
    }
}

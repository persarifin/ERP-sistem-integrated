<?php

namespace App\Http\Resources\Payments;

use App\Http\Resources\BaseResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\Submissions\SubmissionResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CompanyWalletResource;
use App\Http\Resources\Payments\PaymentTransactionAttachmentResource;

class PaymentTransactionResource extends BaseResource
{
    protected $availableRelations = [
        'submission',
        'company',
        'user',
        'company_wallet',
        'payment_transaction_attachment'
    ];
    protected $resourceType = 'payment_transaction';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id'                    => $this->getIdentifier(),
            'amount'                => $this->amount,
            'date'                  => $this->transaction_date,
            'wallet_name'           => $this->company_wallet->wallet_name,
            'company_wallet_id'     => $this->company_wallet_id,
            'submission_id'         => $this->submission_id,
            'submission_status'     => $this->submission->status,
            'submission_category'   => $this->submission->submission_category,
            'company'               => $this->company,
            'company_id'            => $this->company_id,
            'user_id'               => $this->user_id,
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at
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
    public function getUserRelation()
    {
        return new UserResource($this->user);
    }
    public function getCompanyWalletRelation()
    {
        return new CompanyWalletResource($this->company_wallet);
    }
    public function getPaymentTransactionAttachmentRelation()
  {
    return PaymentTransactionAttachmentResource::collection($this->payment_transaction_attachment);
  }
}
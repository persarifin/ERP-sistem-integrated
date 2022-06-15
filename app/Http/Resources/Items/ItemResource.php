<?php

namespace App\Http\Resources\Items;

use App\Http\Resources\BaseResource;
use App\Http\Resources\Products\ProductResource;
use App\Http\Resources\Products\ProductScheduleResource;
use App\Http\Resources\Items\ItemAttachmentResource;
use App\Http\Resources\CompanyResource;
use App\Http\Resources\Submissions\SubmissionResource;

class ItemResource extends BaseResource
{
    protected $availableRelations = ['submission','company','product','product_schedule','item_attachment'];
    protected $resourceType = 'item';
    
    public function toArray($request)
    {
        return $this->transformResponse([
            'id'                    => $this->getIdentifier(),
            'item_name'             => $this->item_name,
            'quantity'              => $this->quantity,
            'buying_price'          => $this->buying_price,
            'selling_price'         => $this->selling_price,
            'discount'              => $this->discount,
            'tax'                   => $this->tax,
            'product_schedule'      => $this->product_schedule,
            'item_attachment'       => $this->item_attachment,
            'submission_name'       => isset($this->submission) ? $this->submission->submission_name : "-",
            'submission_status'     => isset($this->submission) ? $this->submission->status : "-",
            'submission_type'       => isset($this->submission) ? $this->submission->submission_category->submission_type : "-",
            'submission_id'         => $this->submission_id,
            'company_id'            => $this->company_id,
            'product_id'            => $this->product_id,
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
    public function getItemAttachmentRelation()
    {
        return ItemAttachmentResource::collection($this->item_attachment);
    }
    public function getProductRelation()
    {
        return new ProductResource($this->product);
    }
    public function getProductScheduleRelation()
    {
        return new ProductScheduleResource($this->product_schedule);
    }
}
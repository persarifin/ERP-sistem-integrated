<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'submission_name', 
        'amount',
        'status', 
        'reference_doc_number', 
        'date', 
        'due_date', 
        'user_id',
        'company_id',
        'partner_id',
        'category_id',
        'fullfilment', 
        'description',
        'created_at'
    ];

    protected $dates = ['deleted_at'];

    protected $appends = ['paid'];

    public function getPaidAttribute()
    {
        return PaymentTransaction::where('submission_id', $this->id)->sum('amount');
        
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'partner_id')->with('roles');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function submission_category()
    {
        return $this->belongsTo(SubmissionCategory::class,'category_id');
    }
    
    // has relation include
    public function item()
    {
        return $this->hasMany(Item::class,'submission_id')->with(['product_schedule', 'item_attachment']);
    }

    public function payment_transaction()
    {
        return $this->hasMany(PaymentTransaction::class,'submission_id');
    }

    public function submission_attachment()
    {
        return $this->hasMany(SubmissionAttachment::class,'submission_id');
    }
    
    public function read() 
    {
        return $this->hasMany(ReadStatus::class, 'table_row_id')->where(['read_table'=> 'submission'])->with('user_read');
    }
}

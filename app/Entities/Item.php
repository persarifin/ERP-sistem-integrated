<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Criterias\SearchCriteria;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_name',
        'quantity',
        'buying_price',
        'selling_price',
        'discount',
        'tax',
        'submission_id',
        'company_id',
        'product_id'
    ];
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function item_attachment()
    {
        return $this->hasMany(ItemAttachment::class,'item_id');
    }
    public function product_schedule()
    {
        return $this->hasOne(ProductSchedule::class, 'item_id');
    }
    // protected $appends = ['price'];
    //ambil max value dari item yang udh distinct
}

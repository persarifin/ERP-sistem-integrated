<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategoryAttachment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'category_id',
        'company_id',
        'file_name',
        'file_location'
    ];
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}

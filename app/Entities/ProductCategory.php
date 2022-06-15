<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_name',
        'company_id',
        'description'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function product_category_attachment()
    {
        return $this->hasMany(ProductCategoryAttachment::class,'category_id');
    }

    public function product()
    {
        return $this->hasMany(Product::class,'category_id');
    }
}

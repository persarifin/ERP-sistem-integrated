<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'product_name',
        'product_type',
        'description',
        'stock',
        'unit',
        'min_stock',
        'already_sold',
        'buying_price',
        'selling_price',
        'status',
        'company_id',
        'category_id'
    ];

    public function product_category()
    {
        return $this->belongsTo(ProductCategory::class,'category_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function product_attachment()
    {
        return $this->hasMany(ProductAttachment::class,'product_id');
    }
    public function product_visibility()
    {
        return $this->belongsToMany(InterfaceApp::class, 'product_visibilities','product_id','interface_id');
    }
    public function product_schedule()
    {
        return $this->hasMany(ProductSchedule::class, 'product_id');
    }    
}

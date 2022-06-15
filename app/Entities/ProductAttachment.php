<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductAttachment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'file_name',
        'file_location',
        'attachment_type',
        'company_id',
        'product_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

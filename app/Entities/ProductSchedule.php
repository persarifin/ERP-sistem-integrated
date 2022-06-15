<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSchedule extends Model
{
    use HasFactory;
    protected $fillable = [
        'start',
        'finish',
        'item_id',
        'product_id',
        'company_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class,'item_id');
    }
}

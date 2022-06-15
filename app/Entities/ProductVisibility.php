<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVisibility extends Model
{
    use HasFactory;
    protected $fillable = [
        'interface_id',
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

    public function interface()
    {
        return $this->belongsTo(InterfaceApp::class);
    }

}

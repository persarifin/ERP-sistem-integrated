<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemAttachment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'file_name',
        'file_location',
        'item_id',
        'company_id'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
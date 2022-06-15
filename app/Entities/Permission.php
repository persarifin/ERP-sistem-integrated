<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    public function role()
    {
        $this->belongsToMany(App\Entities\Role::class);
    }
}

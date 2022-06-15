<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];

    public function permission()
    {
        $this->hasMany(App\Entities\Permission::class);
    }

}

<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContentVisibility extends Model
{
    use HasFactory;
    protected $fillable = [
        'content_id',
        'companya_id',
        'interface_id'
    ];

    
    public function content()
    {
      return $this->belongsTo(Content::class);
    }
    
    public function company()
    {
      return $this->belongsTo(Company::class, 'company_id');
    }
    
    public function Interface()
    {
      return $this->belongsTo(InterfaceApp::class);
    }
}

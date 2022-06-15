<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Content extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'content_name',
        'content',
        'date',
        'status',
        'company_id',
        'user_id',
        'category_id'
    ];

    protected $table = 'contents';

    public function content_attachment()
    {
        return $this->hasMany(ContentAttachment::class,'content_id');
    }
    public function content_comment()
    {
        return $this->hasMany(ContentComment::class,'content_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function content_category()
    {
        return $this->belongsTo(ContentCategory::class);
    }
    public function content_visibility()
    {
        return $this->hasOne(ContentVisibility::class,'content_id');
    }


}

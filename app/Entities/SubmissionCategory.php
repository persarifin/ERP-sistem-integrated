<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'category_name',
        'maximum',
        'submission_type',
        'company_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
    public function submission()
    {
        return $this->hasMany(Submission::class,'category_id');
    }
}

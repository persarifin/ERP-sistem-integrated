<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubmissionComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'comment',
        'date',
        'submission_id',
        'company_id',
        'user_id'
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubmissionAttachment extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'file_location',
        'file_name',
        'attachment_type',
        'submission_id',
        'company_id'
    ];
    protected $table = 'submission_attachments';

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}

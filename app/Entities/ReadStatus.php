<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadStatus extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'read_table',
        'table_row_id'
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
    public function user_read()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

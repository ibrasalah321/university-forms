<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionValue extends Model
{
    protected $fillable = [
        'submission_id',
        'field_id',
        'value',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function field()
    {
        return $this->belongsTo(FormField::class, 'field_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'form_id',
        'ip_address',
        'user_agent',
        'submitted_at',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function values()
    {
        return $this->hasMany(SubmissionValue::class);
    }
}

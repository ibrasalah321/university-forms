<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    protected $fillable = [
        'form_id',
        'label',
        'field_type',
        'is_required',
        'unique_check',
        'validation_rules',
        'options',
        'placeholder',
        'help_text',
        'sort_order',
    ];

    protected $casts = [
        'validation_rules' => 'array',
        'options' => 'array',
        'is_required' => 'boolean',
        'unique_check' => 'boolean',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function values()
    {
        return $this->hasMany(SubmissionValue::class, 'field_id');
    }
}

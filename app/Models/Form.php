<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Form extends Model
{
    protected $fillable = [
        'uuid',
        'title',
        'description',
        'max_submissions',
        'start_at',
        'end_at',
        'status',
        'created_by',
    ];

    public function fields()
    {
        return $this->hasMany(FormField::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    


    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    protected static function booted()
    {
        static::creating(function ($form) {

            $form->uuid = Str::uuid();

        });
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    protected $table = 'forms';

    protected $fillable = [
        'id',
        'is_required',
        'job_id',
    ];

    // Define relationships
    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function questions()
    {
        return $this->hasMany(FormQuestion::class, 'form_id');
    }

}

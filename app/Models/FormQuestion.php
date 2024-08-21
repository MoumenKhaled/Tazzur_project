<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormQuestion extends Model
{
    use HasFactory;
    protected $table = 'form_question';

    protected $fillable = [
        'form_id',
        'question',
    ];

    // Define relationships
    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function options()
    {
        return $this->hasMany(FormOption::class, 'question_id');
    }

}

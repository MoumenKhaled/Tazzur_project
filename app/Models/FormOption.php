<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormOption extends Model
{
    use HasFactory;
    protected $table = 'form_options';

    protected $fillable = [
        'option_text',
        'question_id',
    ];

    // Define relationships
    public function question()
    {
        return $this->belongsTo(FormQuestion::class, 'question_id');
    }

}

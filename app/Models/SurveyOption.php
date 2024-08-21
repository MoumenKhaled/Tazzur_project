<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyOption extends Model
{
    use HasFactory;
    protected $table='surveys_options';
    protected $guarded=[''];
    
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}

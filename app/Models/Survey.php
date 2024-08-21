<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SurveyOption;
class Survey extends Model
{
    use HasFactory;
    protected $table='surveys';
    protected $guarded=[''];
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function options()
    {
        return $this->hasMany(SurveyOption::class);
    }

    public function votes()
    {
    return $this->hasMany(Vote::class);
    }
}

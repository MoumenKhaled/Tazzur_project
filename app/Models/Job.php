<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = 'job';

    protected $fillable = [
        'hidden_name',
        'job_title',
        'number_employees',
        'topic',
        'job_environment',
        'salary_fields',
        'education_level',
        'require_qualifications',
        'special_qualifications',
        'is_required_image',
        'required_languages',
        'experiense_years',
        'gender',
        'location',
        'company_id',
        'is_required_license',
        'status',
        'is_required_military',
        'job_time',
        'views',
        'end_date',
    ];

    // Define relationships if applicable
    public function forms()
    {
        return $this->hasMany(Form::class, 'job_id');
    }
    public function jobapplications()
    {
        return $this->hasMany(JobApplication::class, 'job_id');
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getLocationMapAttribute($value)
    {
        return json_decode($value, true);
    }
    public function getTopicAttribute($value)
    {
      
    return trans('data.'.$value);
    }
    public function getEducationLevelAttribute($value)
    {
        return trans('data.'.$value);
    }
    public function getExperienseYearsAttribute($value)
    {
        return trans('data.'.$value);
    }
    public function getLocationAttribute($value)
    {
        return trans('data.'.$value);
    }
    
    
      public function getJobTimeAttribute($value)
{
     $typeArray = json_decode($value, true);
    if (!is_array($typeArray)) {
        $cleanedValue = stripslashes(trim($value, '"'));
        $typeArray = json_decode($cleanedValue, true);
    }
    if (!is_array($typeArray)) {
        return trans('data.'.$value);
    }
    $result = array_map(function($item) {
        $item = stripslashes(trim($item, '"'));
        $decodedItem = json_decode($item, true);
        return is_array($decodedItem) ? trans('data.'.$decodedItem[0]) : trans('data.'.$item);
    }, $typeArray);
    return implode(' - ', $result);
}
public function getGenderAttribute($value)
{
    $typeArray = json_decode($value, true);
    if (!is_array($typeArray)) {
        $cleanedValue = stripslashes(trim($value, '"'));
        $typeArray = json_decode($cleanedValue, true);
    }
    if (!is_array($typeArray)) {
        return trans('data.'.$value);
    }
    $result = array_map(function($item) {
        $item = stripslashes(trim($item, '"'));
        $decodedItem = json_decode($item, true);
        return is_array($decodedItem) ? trans('data.'.$decodedItem[0]) : trans('data.'.$item);
    }, $typeArray);
    return implode(' - ', $result);
}

  public function getJobEnvironmentAttribute($value)
{
       $typeArray = json_decode($value, true);
    if (!is_array($typeArray)) {
        $cleanedValue = stripslashes(trim($value, '"'));
        $typeArray = json_decode($cleanedValue, true);
    }
    if (!is_array($typeArray)) {
        return trans('data.'.$value);
    }
    $result = array_map(function($item) {
        $item = stripslashes(trim($item, '"'));
        $decodedItem = json_decode($item, true);
        return is_array($decodedItem) ? trans('data.'.$decodedItem[0]) : trans('data.'.$item);
    }, $typeArray);
    return implode(' - ', $result);
}
  public function getIsRequiredMilitaryAttribute($value)
{
     $typeArray = json_decode($value, true);
    if (!is_array($typeArray)) {
        $cleanedValue = stripslashes(trim($value, '"'));
        $typeArray = json_decode($cleanedValue, true);
    }
    if (!is_array($typeArray)) {
        return trans('data.'.$value);
    }
    $result = array_map(function($item) {
        $item = stripslashes(trim($item, '"'));
        $decodedItem = json_decode($item, true);
        return is_array($decodedItem) ? trans('data.'.$decodedItem[0]) : trans('data.'.$item);
    }, $typeArray);
    return implode(' - ', $result);
}
}

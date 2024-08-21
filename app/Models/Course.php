<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $guarded = [''];
    public function courseApplications()
    {
        return $this->hasMany(CourseApplication::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function getTopicAttribute($value)
    {
        return trans('data.'.$value);
    }


    public function getLocationAttribute($value)
    {
        return trans('data.'.$value);
    }
    public function getTypeAttribute($value)
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

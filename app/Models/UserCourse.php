<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    use HasFactory;
    protected $table = 'usercv_courses';
    protected $fillable = [
        'name',
        'source',
        'duration',
        'user_id',
        'image',
        'details',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getImageAttribute($value)
    {
        return url('http://86.38.218.161:8080/' . $value);
    }
}

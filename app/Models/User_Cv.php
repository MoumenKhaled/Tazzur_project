<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_Cv extends Model
{
    protected $table='user_cv';
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getImageAttribute($value)
    {
        return url('http://86.38.218.161:8080/' . $value);
    }
    public function getCvFileAttribute($value)
    {
        return url('http://86.38.218.161:8080/' . $value);
    }

}

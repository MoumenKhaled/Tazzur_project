<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserConsultation extends Model
{
    use HasFactory;
    protected $table = 'users_consultation';
    protected $guarded=[''];
    public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

public function advisor()
{
    return $this->belongsTo(Advisor::class, 'advisor_id');
}
}

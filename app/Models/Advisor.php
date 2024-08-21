<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Tymon\JWTAuth\Contracts\JWTSubject;

class Advisor extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $guard='advisor';
    protected $table='advisors';
    protected $guarded=[''];
    protected $hidden = [
        'password',
        'remember_token',

    ];

    public function consultations()
    {
        return $this->hasMany(\App\Models\CompanyConsulution::class, 'advisor_id');
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function user_consultations()
    {
        return $this->hasMany(UserConsultation::class, 'advisor_id');
    }
    public function getTopicsAttribute($value)
    {
        return trans($value);
    }
}

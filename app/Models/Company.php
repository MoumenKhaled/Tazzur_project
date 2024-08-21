<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Company extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $guard = 'company';
    protected $table = 'companies';
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at',
        'otp_code',
        'is_complete',
        'email_verification',
        'is_verified',
        'email_verified_at',
    ];
    protected $guarded = [''];

    protected $casts = [

        'documents' => 'json',
        'type' => 'json',
        'location_map' => 'json',
    ];
    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function survey()
    {
        return $this->hasMany(Survey::class);
    }
    public function followers()
    {
        return $this->hasMany(Follower::class, 'company_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getLogoAttribute($value)
    {
        return url('http://86.38.218.161:8080/' . $value);
    }
    // public function getTypeAttribute($value)
    // {
    //     return json_decode($value, true);
    // }
public function getTopicAttribute($value)
{
 \Log::error('value:', ['value' => $value]);
    return trans('data.'.$value);
}

    public function getLocationAttribute($value)
    {
        return trans('data.'.$value);
    }


  public function getTypeAttribute($value)
{
  \Log::error('test');
    $types = json_decode($value, true);


    if (is_null($types)) {
        \Log::error('Failed to types:', ['types' => $types]);
        return null;
    }
   \Log::error('types:', ['types' => $types]);
    if (is_array($types)) {
        return array_map(function ($type) {
            return trans('data.'.$type);
        }, $types);
    }

    return trans($types);
}







}

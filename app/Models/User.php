<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'name',
    //     'email',
    //     'password',
    // ];
    protected $guarded=[''];
    protected $table='users';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tppic' => 'json',
    ];

           /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function user_cv()
    {
        return $this->hasOne(User_Cv::class);
       // return $this->belongsTo(\App\Models\User_Cv::class, 'user_id');
    }
    public function jobapplications()
    {
        return $this->hasMany(JobApplication::class, 'user_id');
    }

    public function courseApplications()
    {
    return $this->hasMany(CourseApplication::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
    public function followers()
    {
    return $this->hasMany(Follower::class, 'user_id');
    }
    public function links()
    {
        return $this->hasMany(UserLink::class);
    }
    public function experiences()
{
    return $this->hasMany(Experience::class);
}
    public function cvCourses()
{
    return $this->hasMany(UserCourse::class);
}
    public function references()
{
    return $this->hasMany(UserReference::class);
}
    public function consultations()
{
    return $this->hasMany(UserConsultation::class, 'user_id');
}
public function getTopicAttribute($value)
    {
        return is_array($value) ? $value : json_decode($value, true);
    }

}

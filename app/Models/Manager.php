<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class Manager extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $guard='manager';
    protected $table='managers';
    protected $fillable = ['email', 'password', 'role_name', 'name'];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = ['role_name' => 'json'];

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
  public function getRoleNameAttribute($value)
{
  \Log::error('test');
    $roles = json_decode($value, true);


    if (is_null($roles)) {
        \Log::error('Failed to decode role_name:', ['value' => $value]);
        return null;
    }
   \Log::error('roles:', ['roles' => $roles]);
    if (is_array($roles)) {
        return array_map(function ($role) {
            return trans($role);
        }, $roles);
    }

    return trans($roles);
}

}

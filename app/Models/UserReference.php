<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReference extends Model
{
    use HasFactory;
    protected $table = 'user_references';
    protected $fillable = [
        'name',
        'employment',
        'email',
        'user_id',
        'phone',

    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

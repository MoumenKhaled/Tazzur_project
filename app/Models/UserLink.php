<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLink extends Model
{
    use HasFactory;
    protected $table = 'user_links';

    protected $fillable = ['title', 'link', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

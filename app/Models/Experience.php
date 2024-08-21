<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasFactory;
    protected $table = 'experiences';
    protected $fillable = [
        'user_id',
        'company_name',
        'job_title',
        'start_date',
        'end_date',
        'details',
        'name',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

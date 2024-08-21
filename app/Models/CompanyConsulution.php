<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Company;

class CompanyConsulution extends Model
{
    use HasFactory;
    protected $table='companies_consultation';
    protected $guarded=[''];
    public function company()
{
    return $this->belongsTo(Company::class, 'company_id');
}

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient_medicine extends Model
{
    use HasFactory;

    protected $table="patient_medicine";
    protected $fillable=['medicine_code','patient_code','c_home_code','remark','updated_by_user','doses'];

    public function patientname()
    {
        return $this->hasOne(User::class, 'id', 'patient_code');
    }

    public function medicine()
    {
        return $this->hasOne(Medicine::class, 'id', 'medicine_code');
    }
    
    public function carehome()
    {
        return $this->hasOne(Company::class, 'id', 'c_home_code');
    }
}

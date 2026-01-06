<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient_disease extends Model
{
    use HasFactory;

    protected $table="patient_disease";
    protected $fillable=['disease_code','patient_code','c_home_code','remark','updated_by_user'];

    public function patientname()
    {
        return $this->hasOne(User::class, 'id', 'patient_code');
    }

    public function disease()
    {
        return $this->hasOne(Decease::class, 'id', 'disease_code');
    }
    
    public function carehome()
    {
        return $this->hasOne(Company::class, 'id', 'c_home_code');
    }








}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient_schedule extends Model
{
    use HasFactory;
    protected $table="patient_schedule";
    protected $fillable=['patient_id','date','time','carer_code','carer_assigned_by','alternate_carer_code','remarks','attended_remarks','attended','attended_on_time','company'];

    public function patientname()
    {
        return $this->hasOne(User::class, 'id', 'patient_id');
    }

    public function carername()
    {
        return $this->hasOne(User::class, 'id', 'carer_code');
    }

    public function alternatecarername()
    {
        return $this->hasOne(User::class, 'id', 'alternate_carer_code');
    }

    public function role()
    {
        return $this->hasOne(User::class, 'id', 'carer_assigned_by');
    }

    public function comp()
    {
        return $this->belongsTo(Company::class, 'company');
    }


}

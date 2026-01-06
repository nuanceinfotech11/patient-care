<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient_carer extends Model
{
    use HasFactory;

    protected $table="patient_carer_map";
    protected $fillable=['patient_id','carer_id','company'];

    public function patientname()
    {
        return $this->hasOne(User::class, 'id', 'patient_id');
    }

    public function carername()
    {
        return $this->hasOne(User::class, 'id', 'carer_id');
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

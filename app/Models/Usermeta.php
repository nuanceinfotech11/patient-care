<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usermeta extends Model
{
    use HasFactory;
    protected $table="user_meta";

    protected $fillable = [
        'care_home_code',
        'father_husband name',
        'dob',
        'marital_status',
        'anniversary',
        'special_instructions',
        'updated_by_user'
        
    ];

    public function company()
    {
    return $this->hasOne(User::class, 'id', 'u_id');//company data we will get by this
    }


}

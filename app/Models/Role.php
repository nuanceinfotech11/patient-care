<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    
    public function users() {

        return $this->belongsToMany(User::class,'users_roles');
            
    }
    public function company()
    {
    return $this->belongsToMany(Company::class, 'company_user_mappings', 'user_id', 'company_id');//company data we will get by this
    }
}

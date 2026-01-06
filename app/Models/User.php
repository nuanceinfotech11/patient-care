<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use App\Models\Company;
use App\Models\Permission;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable,SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'code',
        'name',
        'password2',
        'email',
        'password',
        'country',
        'state',
        'city',
        'address',
        'address1',
        'address2',
        'address3',
        'zipcode',
        'phone',
        'website_url',
        'image',
        'blocked',
        'user_type',
        'invite_code',
        'typeselect',
        'option_for_block'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
    return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id');
    }

    

    public function company()
    {
      return $this->belongsToMany(Company::class, 'company_user_mappings', 'user_id', 'company_id');//company data we will get by this
    }

    public function permission()
    {
        return $this->hasMany(Permission::class,'user_id');

    }

    public function buyertypechannelName()
    {
        return $this->hasMany(BuyerTypeChannel::class,'id','user_type');

    }
    public function countryname()
    {
        return $this->hasOne(Country::class, 'id', 'country');
    }
    public function statename()
    {
        return $this->hasOne(State::class, 'id', 'state');
    }
    public function cityname()
    {
        return $this->hasOne(City::class, 'id', 'city');
    }

    
}

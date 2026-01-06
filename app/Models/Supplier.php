<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table='supplier';
    protected $fillable=[
    'code',
    'name',
    'email',
    'country',
    'state',
    'city',
    'address1',
    'address2',
    'address3',
    'zipcode',
    'phone'
    ];

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

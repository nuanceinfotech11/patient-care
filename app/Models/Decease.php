<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decease extends Model
{
    use HasFactory;
    protected $table="disease";
    protected $fillable = [
        'code',
        'name',
        'symptoms',
        'note'
       
    ];

    public function comp()
    {
        return $this->belongsTo(Company::class, 'company');
    }

}

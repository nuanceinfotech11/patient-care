<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;
    protected $table="medicine";
    protected $fillable=["company","medicine_name","quantity"];

    public function comp()
    {
        return $this->belongsTo(Company::class, 'company');
    }
}

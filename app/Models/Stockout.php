<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockout extends Model
{
    use HasFactory;
    protected $table="stock_out";
    protected $fillable=['doc_no','date','patient_code','carer_code','inventory_code','quantity','stock_out_by'];

    public function inventorynameout()
    {
        return $this->hasOne(Inventory::class, 'id', 'inventory_code');
    }
    public function patientname()
    {
        return $this->hasOne(User::class, 'id', 'patient_code');
    }
    public function carername()
    {
        return $this->hasOne(User::class, 'id', 'carer_code');
    }
}

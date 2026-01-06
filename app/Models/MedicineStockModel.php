<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\{Medicine,Company,MedicineStockModel};

class MedicineStockModel extends Model
{
    use HasFactory;
   // protected $table="medicine";
    protected $fillable=["id","company_id","medicine_id", "dates", "quantity", "purchase_issue_type"];

    public function company()
    {
        return $this->hasOne(Company::class, 'id','company_id');
    }

    public function medicine()
    {
        return $this->hasOne(Medicine::class, 'id','medicine_id');
    }
}

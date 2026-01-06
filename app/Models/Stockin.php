<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockin extends Model
{
    use HasFactory;
    protected $table="stock_in";
    protected $fillable=['doc_no','date','supplier_code','inventory_code','quantity','rate','stock_in_by','supplier_doc_no'];

    public function inventoryname()
    {
        return $this->hasOne(Inventory::class, 'id', 'inventory_code');
    }
}

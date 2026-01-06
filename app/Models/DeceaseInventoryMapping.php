<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeceaseInventoryMapping extends Model
{
    use HasFactory;
    protected $table ='decease_inventory_mapping';
    protected $fillable=['decease_id','inventory_id'];

    public function decease()
    {
        return $this->hasOne(Decease::class, 'id', 'decease_id');
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'id', 'inventory_id');
    }
    
}

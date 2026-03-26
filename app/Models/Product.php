<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'id_company',
        'id_category',
        'name',
        'description',
        'price',
        'status'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime'
    ];
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }
    
    public function branchInventories()
    {
        return $this->hasMany(BranchInventory::class, 'id_product');
    }
    
    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class, 'id_product');
    }
    
    public function getStatusTextAttribute()
    {
        return $this->status === 'active' ? 'Activo' : 'Inactivo';
    }
    
    public function getStatusClassAttribute()
    {
        return $this->status === 'active' ? 'success' : 'danger';
    }
}
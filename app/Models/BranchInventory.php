<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BranchInventory extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    
    protected $table = 'branch_inventory';
    
    protected $fillable = [
        'id_branch',
        'id_product',
        'stock'
    ];
    
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
    
    // Accessor para estado del stock
    public function getStockStatusAttribute()
    {
        if ($this->stock <= 10) {
            return ['class' => 'critical', 'text' => 'Stock Crítico', 'color' => '#dc3545'];
        } elseif ($this->stock <= 30) {
            return ['class' => 'low', 'text' => 'Stock Bajo', 'color' => '#ffc107'];
        } else {
            return ['class' => 'normal', 'text' => 'Suficiente', 'color' => '#28a745'];
        }
    }
    
    // Accessor para el color del estado
    public function getStockStatusColorAttribute()
    {
        return $this->stockStatus['color'];
    }
    
    // Accessor para el texto del estado
    public function getStockStatusTextAttribute()
    {
        return $this->stockStatus['text'];
    }
}
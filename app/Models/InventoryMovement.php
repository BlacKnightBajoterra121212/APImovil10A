<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventoryMovement extends Model
{
    use HasFactory;
    
    public $timestamps = false; // Porque no tienes created_at/updated_at
    
    protected $table = 'inventory_movements';
    
    protected $fillable = [
        'id_branch',
        'id_product',
        'movement_type',
        'quantity',
        'reason',
        'movement_date'
    ];
    
    protected $casts = [
        'movement_date' => 'datetime',
        'quantity' => 'integer'
    ];
    
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
    
    // Accessor para tipo de movimiento en texto
    public function getMovementTypeTextAttribute()
    {
        return $this->movement_type === 'in' ? 'Entrada' : 'Salida';
    }
    
    // Accessor para clase CSS del movimiento
    public function getMovementTypeClassAttribute()
    {
        return $this->movement_type === 'in' ? 'success' : 'warning';
    }
    
    // Accessor para mostrar cantidad con signo
    public function getQuantityWithSignAttribute()
    {
        return $this->movement_type === 'in' ? '+' . $this->quantity : '-' . $this->quantity;
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDetail extends Model
{
    use HasFactory;
    
    protected $table = 'order_details';
    
    protected $fillable = [
        'id_order',
        'id_product',
        'quantity',
        'unit_price',
        'subtotal'
    ];
    
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function order()
    {
        return $this->belongsTo(Order::class, 'id_order');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_product');
    }
}
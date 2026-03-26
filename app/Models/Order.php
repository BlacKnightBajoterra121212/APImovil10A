<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    
    protected $table = 'orders';
    
    protected $fillable = [
        'id_company',
        'id_branch',
        'id_client',
        'id_user',
        'total',
        'status',
        'order_date'
    ];
    
    protected $casts = [
        'total' => 'decimal:2',
        'order_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }
    
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch');
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class, 'id_client');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'id_order');
    }
    
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Pendiente',
            'preparing' => 'En Preparación',
            'ready' => 'Listo para Entregar',
            'delivered' => 'Entregado',
            'cancelled' => 'Cancelado'
        ];
        return $statuses[$this->status] ?? $this->status;
    }
    
    public function getStatusClassAttribute()
    {
        $classes = [
            'pending' => 'warning',
            'preparing' => 'info',
            'ready' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger'
        ];
        return $classes[$this->status] ?? 'secondary';
    }
}
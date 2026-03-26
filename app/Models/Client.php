<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;
    
    protected $table = 'clients';
    
    protected $fillable = [
        'id_company',
        'name',
        'phone',
        'email',
        'address',
        'status'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }
    
    public function orders()
    {
        return $this->hasMany(Order::class, 'id_client');
    }
    
    // Accessor para estado
    public function getStatusTextAttribute()
    {
        return $this->status === 'active' ? 'Activo' : 'Inactivo';
    }
    
    public function getStatusClassAttribute()
    {
        return $this->status === 'active' ? 'success' : 'danger';
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Branch extends Model
{
    use HasFactory;
    
    public $timestamps = false; // Si no quieres timestamps
    
    protected $fillable = [
        'id_company',
        'name',
        'address',
        'phone',
        'status'
    ];
    
    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }
    
    public function users()
    {
        return $this->hasMany(User::class, 'id_branch');
    }
    
    public function getStatusTextAttribute()
    {
        return $this->status === 'active' ? 'Activa' : 'Inactiva';
    }
    
    public function getStatusClassAttribute()
    {
        return $this->status === 'active' ? 'online' : 'offline';
    }
}
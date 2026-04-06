<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'id_company',
        'name',
        'address',
        'phone',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_branch');
    }

    public function activeUsers()
    {
        return $this->hasMany(User::class, 'id_branch')->where('status', 'active');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_branch');
    }

    public function inventories()
    {
        return $this->hasMany(BranchInventory::class, 'id_branch');
    }

    public function encargado()
    {
        return $this->hasOne(User::class, 'id_branch')
            ->where('status', 'active')
            ->whereHas('role', function ($roleQuery) {
                $roleQuery->where('name', 'LIKE', '%encarg%');
            })
            ->orderBy('id');
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

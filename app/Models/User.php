<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id_company',
        'id_role',
        'id_branch',
        'name',
        'email',
        'password',
        'phone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con rol
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    // Relación con compañía
    public function company()
    {
        return $this->belongsTo(Company::class, 'id_company');
    }

    // Relación con sucursal
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_branch');
    }
}

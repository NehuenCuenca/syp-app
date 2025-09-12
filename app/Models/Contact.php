<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'company_name',
        'contact_name',
        'email',
        'phone',
        'address',
        'contact_type',
        'registered_at'
    ];

    protected $casts = [
        'registered_at' => 'datetime'
    ];
    
    // Scopes para filtrar por tipo de contacto
    public function scopeClients($query)
    {
        return $query->where('contact_type', 'Cliente');
    }

    public function scopeSuppliers($query)
    {
        return $query->where('contact_type', 'Proveedor');
    }
}
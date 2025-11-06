<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
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

    protected $appends = [
        'full_name',
        'last_order'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'id_contact');
    }

    public function getFullNameAttribute()
    {
        return $this->company_name . ' (' . $this->contact_type . ')';
    }

    public function getLastOrderAttribute()
{
    $lastOrder = $this->orders()
        ->with(['movementType'])
        ->select('id', 'id_movement_type', 'total_net', 'created_at') //aca falta el movementType
        ->orderBy('created_at', 'desc')
        ->first();
    
    if (!$lastOrder) {
        return 'Ultimo pedido: ---';
    }
    
    // Retornar un array con los datos necesarios y el alias creado manualmente
    return  'Ultima ' . strtolower($lastOrder->movementType->name) . ': '
                       . $lastOrder->created_at->format('Y-m-d');
}
    
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
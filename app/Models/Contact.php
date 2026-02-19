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
        'name',
        'email',
        'phone',
        'address',
        'contact_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s'
    ];

    protected $appends = [
        'search_alias',
        'phone_number_info',
        'last_order'
    ];

    /**
     * Constants for contacts types
     */
    const CONTACT_TYPE_CLIENT = 'cliente';
    const CONTACT_TYPE_SUPPLIER = 'proveedor';    

    public function orders()
    {
        return $this->hasMany(Order::class, 'contact_id') ;
    }

    public function getFullNameAttribute()
    {
        return $this->name . ' (' . $this->contact_type . ')';
    }

    public function getSearchAliasAttribute()
    {
        $is_deleted = ($this->trashed()) ? '**BORRADO**' : '';
        return "{$is_deleted}{$this->code}| {$this->name}";
    }

    public function getPhoneNumberInfoAttribute()
    {
        return (!$this->phone) ? "Telefono sin registrar" : $this->phone;
    }

    public function getLastOrderAttribute()
    {
        $lastOrder = $this->orders()
                            ->with(['movementType'])
                            ->select('id', 'id_movement_type', 'total_net', 'created_at')
                            ->orderBy('created_at', 'desc')
                            ->first();
        
        if (!$lastOrder) {
            return 'No tiene ultimo pedido';
        }
        
        // Retornar un array con los datos necesarios y el alias creado manualmente
        return  'Ultima ' . strtolower($lastOrder->movementType->name) . ': '
                        . $lastOrder->created_at->format('d/m/Y');
    }
    
    public static function getContactTypes(){
        return [
            self::CONTACT_TYPE_CLIENT,
            self::CONTACT_TYPE_SUPPLIER,
        ];
    } 
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_contact',
        'id_user_creator',
        'estimated_delivery_date',
        'actual_delivery_date',
        'order_type',
        'order_status',
        'total_gross',
        'total_taxes',
        'total_net',
        'notes'
    ];

    protected $casts = [
        'estimated_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'total_gross' => 'decimal:2',
        'total_taxes' => 'decimal:2',
        'total_net' => 'decimal:2'
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'id_contact');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_creator');
    }
}
<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'buy_price',
        'profit_percentage',
        'sale_price',
        'current_stock',
        'min_stock_alert',
        'category_id'
    ];

    protected $casts = [
        'buy_price' => 'integer',
        'profit_percentage' => 'integer',
        'sale_price' => 'integer',
        'current_stock' => 'integer',
        'min_stock_alert' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'last_buyed_at'     => 'datetime',
    ];

    protected $appends = [
        'search_alias',
        'stock_availability',
        'is_low_stock',
        'is_empty_stock',
        'sale_price_as_currency'
    ];

    public function getIsLowStockAttribute()
    {
        return $this->current_stock <= $this->min_stock_alert;
    }

    public function getIsEmptyStockAttribute()
    {
        return $this->current_stock <= 0;
    }

    public function getSearchAliasAttribute()
    {
        $is_deleted = ($this->trashed()) ? '(BORRADO)' : '';
        return "{$is_deleted}{$this->code}| {$this->name}";
    }

    public function getStockAvailabilityAttribute()
    {
        if($this->current_stock > 0){
            return custom_format_number($this->current_stock);
        } else{
            return 'Agotado';
        }
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    
/**
     * Accessor para formatear last_buyed_at como:
     * "Hace X días (d-m-Y)".
     */
    public function getLastBuyedAtAttribute($value): ?string
    {
        if (!$value) {
            // Nunca se compró -> puedes devolver null o un texto como "Nunca comprado"
            return null;
        }

        $date = $value instanceof Carbon ? $value : Carbon::parse($value);

        // Calculamos la diferencia en días "enteros"
        $daysDiff = $date->startOfDay()->diffInDays(now()->startOfDay());

        $formattedDate = $date->format('d-m-Y');

        if ($daysDiff === 0) {
            return "Hoy ({$formattedDate})";
        }

        if ($daysDiff === 1) {
            return "Hace 1 día ({$formattedDate})";
        }

        return "Hace {$daysDiff} días ({$formattedDate})";
    }


    public function calculateSellPrice()
    {
        return (int)($this->buy_price * (1 + $this->profit_percentage / 100));
    }

    public function getSalePriceAsCurrencyAttribute(): string
    {
        return format_number_to_currency($this->sale_price);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $appends = [
        'search_alias'
    ];

    // Relación: una categoría tiene muchos productos
    public function products()
    {
        return $this->hasMany(Product::class, 'id_category');
    }

    public function getSearchAliasAttribute()
    {
        return "{$this->id}| {$this->name}";
    }
}

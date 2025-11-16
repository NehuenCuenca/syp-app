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

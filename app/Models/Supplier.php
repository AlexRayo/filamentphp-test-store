<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'phone_number',
        'address',
    ];

    // Relación con la tabla 'products'
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Relación con la tabla 'purchases'
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}

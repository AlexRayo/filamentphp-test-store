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
    // Relación con la tabla 'products'
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

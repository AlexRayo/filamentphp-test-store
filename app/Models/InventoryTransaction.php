<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'quantity',
        'type', // 'in' o 'out'
        'date',
        'comments',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

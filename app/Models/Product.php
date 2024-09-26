<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
        'buy_price',
        'profit_margin',
        'sell_price',
        'discount',
        'iva',
        'stock',
        'category_id',
        'supplier_id',
    ];
    // Relación con la tabla 'categories'
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relación con la tabla 'suppliers'
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // Relación con la tabla 'purchase_details'
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    // Relación con la tabla 'sales_details'
    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    // Relación con la tabla 'inventory_transactions'
    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }
}

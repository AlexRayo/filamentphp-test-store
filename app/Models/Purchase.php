<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_id',
    ];

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

}

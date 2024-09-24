<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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
    // Método para actualizar los campos después de eliminar un detalle de compra
    public function updateAfterDetailRemoved()
    {
        // Recalcular el total de la compra
        $this->total = $this->purchaseDetails->sum(function ($detail) {
            return $detail->quantity * $detail->unit_price;
        });

        // Si no hay detalles de compra, establecer el total a 0
        if ($this->purchaseDetails->isEmpty()) {
            $this->total = 0;
        }

        // Guardar los cambios
        $this->save();
    }
}

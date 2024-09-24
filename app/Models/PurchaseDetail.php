<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_price',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Evento que se ejecuta después de guardar un detalle de compra
    protected static function booted()
    {
        // Actualizar stock cuando se crea un detalle de compra
        static::created(function ($purchaseDetail) {
            $product = $purchaseDetail->product;
            if ($product) {
                Log::info('Stock actualizado para el producto ID: ' . $product->id);
                $product->stock += $purchaseDetail->quantity;
                $product->save();
            }
        });

        // Actualizar stock cuando se modifica un detalle de compra
        // Evento que se ejecuta después de modificar un detalle de compra
        static::updated(function ($purchaseDetail) {
            $product = $purchaseDetail->product;
            if ($product) {
                // Obtener la cantidad anterior antes de la actualización
                $originalQuantity = $purchaseDetail->getOriginal('quantity');
                $newQuantity = $purchaseDetail->quantity;

                // Calcular la diferencia entre las cantidades
                $quantityDifference = $newQuantity - $originalQuantity;

                Log::info("originalQuantity: {$originalQuantity}");
                Log::info("NewQty: {$newQuantity}");
                log::info("Actual Stock: {$product->stock}");
                log::info("Diferencia: {$quantityDifference}");

                // Ajustar el stock en función de la diferencia (si es positiva o negativa)
                $updatedStock = $product->stock + $quantityDifference;
                $product->stock = $updatedStock;
                $product->save();

                Log::info('Stock actualizado a: ' . $updatedStock);
                log::info("*****************************************");
            }
        });


        // Eliminar stock cuando se borra un detalle de compra
        static::deleting(function ($purchaseDetail) {
            $product = $purchaseDetail->product;
            if ($product) {
                // Restar la cantidad del stock cuando se borra un detalle
                $product->stock -= $purchaseDetail->quantity;
                $product->save();

                Log::info('Stock eliminado para el producto ID: ' . $product->id);
            }
        });
    }
}

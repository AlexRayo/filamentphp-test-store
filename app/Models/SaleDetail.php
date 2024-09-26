<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class SaleDetail extends Model
{
  use HasFactory;
  protected $fillable = [
    'sale_id',
    'product_id',
    'quantity',
  ];

  public function sale()
  {
    return $this->belongsTo(Sale::class);
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  protected static function booted()
  {
    Log::info("Booted from saleDetail");
    // Actualizar stock cuando se crea un detalle de compra
    static::created(function ($saleDetail) {
      log::info($saleDetail);
      $product = $saleDetail->product;
      if ($product) {
        Log::info('Stock actualizado para el producto ID: ' . $product->id);
        $product->stock -= $saleDetail->quantity;
        $product->save();
      }
    });

    // Actualizar stock cuando se modifica un detalle de compra
    // Evento que se ejecuta después de modificar un detalle de compra
    static::updated(function ($saleDetail) {
      $product = $saleDetail->product;
      if ($product) {
        // Obtener la cantidad anterior antes de la actualización
        $originalQuantity = $saleDetail->getOriginal('quantity');
        $newQuantity = $saleDetail->quantity;

        // Calcular la diferencia entre las cantidades
        $quantityDifference = $newQuantity - $originalQuantity;

        Log::info("originalQuantity: {$originalQuantity}");
        Log::info("NewQty: {$newQuantity}");
        log::info("Actual Stock: {$product->stock}");
        log::info("Diferencia: {$quantityDifference}");

        // Ajustar el stock en función de la diferencia (si es positiva o negativa)
        $updatedStock = $product->stock - $quantityDifference;
        $product->stock = $updatedStock;
        $product->save();

        Log::info('Stock actualizado a: ' . $updatedStock);
        log::info("*****************************************");
      }
    });


    // Eliminar stock cuando se borra un detalle de compra
    static::deleting(function ($saleDetail) {
      $product = $saleDetail->product;
      if ($product) {
        // Restar la cantidad del stock cuando se borra un detalle
        $product->stock += $saleDetail->quantity;
        $product->save();

        Log::info('Stock eliminado para el producto ID: ' . $product->id);
      }
    });
  }
}

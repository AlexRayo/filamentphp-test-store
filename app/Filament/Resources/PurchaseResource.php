<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Log;

class PurchaseResource extends Resource
{
  protected static ?string $model = Purchase::class;

  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  public static function shouldRegisterNavigation(): bool
  {
    // Solo mostrar en el menú si hay productos
    return Product::exists();
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('supplier_id')
          ->relationship('supplier', 'name')
          ->required()
          ->reactive()
          ->afterStateUpdated(function ($state, callable $set) {
            //reset
            $set('purchaseDetails.*.product_id', "");
            $set('purchaseDetails.*.quantity', 1);
            $set('product.*.unit_price', 0);
            $set('purchaseDetails.*.total_row', 0);
            $set('purchaseDetails.*.total', 0);

          })
          ->label('Supplier'),

        Forms\Components\Repeater::make('purchaseDetails')
          ->relationship('purchaseDetails')
          ->label('Purchase Details')
          ->schema([
            Forms\Components\Select::make('product_id')
              ->searchable()
              ->required()
              ->reactive()
              ->options(function (Get $get) {
                $supplierId = $get('../../supplier_id');  // Obtén el valor de supplier_id de esta forma porque se encuentra fuera de scope del repeater
          
                return Product::where('supplier_id', $supplierId)
                  ->pluck('name', 'id')
                  ->toArray();  // Convierte los resultados a un array                                
              })
              ->afterStateUpdated(function ($get, $set) {
                $product = Product::find($get('product_id'));
                if ($product) {
                  $set('unit_price', $product->price);
                  $set('total_row', $get('quantity') * $product->price);
                  return $product->price;
                }
              }),
            Forms\Components\TextInput::make('quantity')
              ->numeric()
              ->required()
              ->minValue(1)
              ->default(1)
              ->reactive()
              ->label('Quantity'),
            Forms\Components\TextInput::make('unit_price')
              ->numeric()
              ->label('Unit Price')
              ->readOnly()
            ,

            // Campo para mostrar el total por fila
            Forms\Components\Placeholder::make('total_row')
              ->label('Total per Item')
              ->reactive()
              ->content(function ($get) {
                return $get('quantity') * $get('unit_price');
              }),

            Forms\Components\Placeholder::make('test')
              ->label('Supplier ID seleccionado')
              ->reactive()
              ->content(function (Get $get) {
                $supplierId = $get('../../supplier_id');
                return $supplierId ? "Supplier ID: $supplierId" : 'unknown';
              }),
          ])
          ->columns(3)
          ->reactive(),

        Forms\Components\Placeholder::make(name: 'total')
          ->label('Total General')
          ->reactive()
          ->content(function (Get $get) {
            $purchaseDetails = collect($get('purchaseDetails'));

            $total = $purchaseDetails->sum(function ($item) {
              $sub_total = $item['quantity'] * $item['unit_price'];
              return $sub_total++;
            });
            return $total;
          }),
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('date')->label('Purchase Date'),
        TextColumn::make('supplier.name')->label('Supplier'),
        TextColumn::make('purchaseDetails.product.name')
          ->label('Product Name')
          ->formatStateUsing(function ($state) {
            if ($state == "" || $state == null) {
              return 'EMPTY ORDER';
            }
            return $state;
          }),
        TextColumn::make('purchaseDetails.quantity')->label('Quantity'),
        TextColumn::make('purchaseDetails.product.buy_price')->label('Unit Price')
          ->money('USD'),
        TextColumn::make('purchaseDetails')->label('Total')
          ->formatStateUsing(function ($record) {
            if ($record->purchaseDetails->isEmpty()) {
              return 'EMPTY ORDER';
            }

            // Suma de los totales para cada detalle de compra
            $total = 0;
            log::info($record->purchaseDetails);
            foreach ($record->purchaseDetails as $detail) {
              $quantity = $detail->quantity ?? 0;
              $unitPrice = $detail->product->buy_price ?? 0;
              $total += $quantity * $unitPrice;
            }

            return '$' . number_format($total, 2);
          }),
        TextColumn::make('created_at')->label('Created At'),
        TextColumn::make('updated_at')->label('Updated At'),

      ])
      ->filters([
        //
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  //Ovweride this method to allow adding new purchses only if there is some product
  public static function canCreate(): bool
  {
    return Product::exists();
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListPurchases::route('/'),
      'create' => Pages\CreatePurchase::route('/create'),
      'edit' => Pages\EditPurchase::route('/{record}/edit'),
    ];
  }
}
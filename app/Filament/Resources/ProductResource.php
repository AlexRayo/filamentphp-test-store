<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductResource extends Resource
{
  protected static ?string $model = Product::class;

  protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

  //Ovweride this method to allow adding new products only if there is some category
  public static function canCreate(): bool
  {
    return Category::exists() && Supplier::exists();
  }

  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Forms\Components\Select::make('supplier_id')
          ->relationship('supplier', 'name')
          ->label('Supplier')
          ->required(),
        Forms\Components\Select::make('category_id')
          ->relationship('category', 'name')
          ->label('Category')
          ->required(),
        Forms\Components\TextInput::make('name')
          ->label('Product Name')
          ->required(),
        Forms\Components\Textarea::make('description')
          ->label('Description'),
        Forms\Components\TextInput::make('buy_price')
          ->label('Buy Price')
          ->numeric()
          ->reactive()
          ->required(),
        Forms\Components\TextInput::make('profit_margin')
          ->label('Profit Margin (%)')
          ->numeric()
          ->reactive()
          ->required()
          ->default(20),
        Forms\Components\TextInput::make('iva')
          ->label('IVA (%)')
          ->numeric()
          ->reactive()
          ->required()
          ->default(15),
        Forms\Components\TextInput::make('discount')
          ->label('Discount (%)')
          ->numeric()
          ->reactive()
          ->required()
          ->default(0),
        Forms\Components\Placeholder::make('sell_price')
          ->label('SELL PRICE')
          ->reactive()
          ->content(function ($get) {
            $buyPrice = $get('buy_price') ?? 0;
            $profitMargin = $get('profit_margin') ?? 0;
            $iva = $get('iva') ?? 0;
            $discount = $get('discount') ?? 0;

            // Calcula el sell_price
            $sellPrice = ($buyPrice + ($buyPrice * ($iva / 100)) + ($buyPrice * ($profitMargin / 100))) - $discount;
            return '$' . $sellPrice;
          }),
        Forms\Components\Placeholder::make('stock')
          ->label('STOCK')
          ->default(0)
          ->content(function ($state) {
            return $state;
          })
      ]);
  }

  protected static function updateSellPrice($set, $get)
  {
    // Calcula el precio de venta basado en buy_price, IVA y discount
    $buyPrice = $get('buy_price') ?? 0;
    $iva = $get('iva') ?? 0;
    $discount = $get('discount') ?? 0;

    // Calcula el sell_price
    $sellPrice = ($buyPrice + ($buyPrice * ($iva / 100))) - $discount;

    // Actualiza el campo sell_price
    $set('sell_price', $sellPrice);
  }



  public static function shouldRegisterNavigation(): bool
  {
    // Solo mostrar en el menú si hay caterias y proveedores agregados
    return Category::exists() && Supplier::exists();
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('name'),
        TextColumn::make('description'),
        TextColumn::make('price'),
        TextColumn::make('stock'),
        TextColumn::make('category.name')->label('Category'),
        TextColumn::make('supplier.name')->label('Supplier'),
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
      ])
      ->bulkActions([
        Tables\Actions\DeleteBulkAction::make(),
      ]);
    //}
  }

  public static function getRelations(): array
  {
    return [];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListProducts::route('/'),
      'create' => Pages\CreateProduct::route('/create'),
      'edit' => Pages\EditProduct::route('/{record}/edit'),
    ];
  }
}

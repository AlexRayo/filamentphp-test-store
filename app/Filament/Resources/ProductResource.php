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
          ->required()
          ->label('Supplier'),
        Forms\Components\TextInput::make('name')
          ->required()
          ->label('Product Name'),
        Forms\Components\Textarea::make('description')
          ->label('Description'),
        Forms\Components\TextInput::make('price')
          ->numeric()
          ->required()
          ->label('Price'),
        Forms\Components\TextInput::make('stock')
          ->numeric()
          ->required()
          ->label('Stock'),
        Forms\Components\Select::make('category_id')
          ->relationship('category', 'name')
          ->required()
          ->label('Category'),
      ]);
  }

  public static function shouldRegisterNavigation(): bool
  {
    // Solo mostrar en el menú si hay caterias y proveedores agregados
    return Category::exists() && Supplier::exists();
  }

  public static function table(Table $table): Table
  {
    // if (!Category::exists()) {
    //     // Si no hay categorías, configuramos el estado vacío con un botón
    //     return $table
    //         ->columns([])  // No mostrar columnas porque no hay datos
    //         ->emptyStateHeading('No categories found')
    //         ->emptyStateDescription('You need to create a category before adding products.')
    //         ->emptyStateActions([
    //             Action::make('Create Category')
    //                 ->url(route('filament.admin.resources.categories.create'))
    //                 ->label('Create Category')
    //                 ->button(),
    //         ]);
    // }
    // else if(!Supplier::exists()){
    //     // Si no hay Suppliers, configuramos el estado vacío con un botón
    //     return $table
    //         ->columns([]) 
    //         ->emptyStateHeading('No suppliers found')
    //         ->emptyStateDescription('You need to create at least one supplier before adding purchases.')
    //         ->emptyStateActions([
    //             Action::make('Create a supplier')
    //                 ->url(route('filament.admin.resources.suppliers.create'))
    //                 ->label('Create a Supplier')
    //                 ->button(),
    //         ]);
    //     } 

    // else {
    // Si hay categorías, mostramos la tabla normal
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

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SaleResource\Pages;
use App\Models\Product;
use App\Models\Sale;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function shouldRegisterNavigation(): bool
    {
        // Solo mostrar en el menú si hay compras
        return Product::exists();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->label('User'),
                Forms\Components\Repeater::make('saleDetails')
                    ->relationship('saleDetails')
                    ->label('Products')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Select Product')
                            ->searchable()
                            ->options(Product::all()->pluck('name', 'id')->toArray())
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $product = Product::find($state);
                                if ($product) {
                                    $set('sell_price', $product->sell_price);  // Establecer precio de venta
                                    $set('quantity', 1);  // Cantidad por defecto
                                }
                            }),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->numeric()
                            ->reactive()
                            ->required()
                            ->default(1)
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $sellPrice = $get('sell_price') ?? 0;
                                $quantity = $get('quantity') ?? 1;
                                $set('total', $sellPrice * $quantity);  // Calcular total
                            }),
                        Forms\Components\TextInput::make('sell_price')
                            ->readOnly()
                            ->label('Sale Price'),
                        Forms\Components\Placeholder::make('total')
                            ->label('Total')
                            ->content(function ($get) {
                                $sellPrice = $get('sell_price') ?? 0;
                                $quantity = $get('quantity') ?? 1;
                                return '$' . number_format($sellPrice * $quantity, 2);
                            }),
                    ])
                    ->columns(4)
                    ->minItems(1),
            ])->columns(1);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->defaultSort('id', 'desc')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSales::route('/'),
            'create' => Pages\CreateSale::route('/create'),
            'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}

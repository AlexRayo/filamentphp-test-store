<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseResource\Pages;
use App\Models\Product;
use App\Models\Purchase;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;

class PurchaseResource extends Resource
{
    protected static ?string $model = Purchase::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->required()
                    ->label('Supplier'),

                Forms\Components\Repeater::make('purchaseDetails')
                    ->relationship('purchaseDetails')                    
                    ->label('Purchase Details')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function($get, $set){
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
                            ->required()
                            ->label('Unit Price')
                            ->readOnly()                           
                            ,

                        // Campo para mostrar el total por fila
                        Forms\Components\Placeholder::make('total_row')
                            ->label('Total per Item')
                            ->reactive()
                            ->content(function($get){
                                return $get('quantity') * $get('unit_price');
                            }),
                    ])
                    ->columns(3)
                    ->reactive(),

                Forms\Components\Placeholder::make('total')
                    ->label('Total General')
                    ->reactive()
                    ->content(function (Get $get) {
                        $purchaseDetails = collect($get('purchaseDetails'));

                        $total = $purchaseDetails->sum(function ($item) {
                            $sub_total =  $item['quantity'] * $item['unit_price'];
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
                TextColumn::make('purchaseDetails.product.name')->label('Product Name'),
                TextColumn::make('purchaseDetails.quantity')->label('Quantity'),
                TextColumn::make('purchaseDetails.unit_price')->label('Unit Price'),
                TextColumn::make('total')->label('Total'),
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
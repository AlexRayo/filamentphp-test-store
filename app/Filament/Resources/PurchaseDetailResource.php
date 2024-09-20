<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseDetailResource\Pages;
use App\Filament\Resources\PurchaseDetailResource\RelationManagers;
use App\Models\PurchaseDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PurchaseDetailResource extends Resource
{
    protected static ?string $model = PurchaseDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\Select::make('purchase_id')
                    ->relationship('purchase', 'id')
                    ->required()
                    ->label('Purchase ID'),
                Forms\Components\Select::make('product_id')
                    ->relationship('product', 'name')
                    ->required()
                    ->label('Product'),
                Forms\Components\TextInput::make('quantity')
                    ->numeric()
                    ->required()
                    ->label('Quantity'),
                Forms\Components\TextInput::make('unit_price')
                    ->numeric()
                    ->required()
                    ->label('Unit Price'),
            ]);
    }    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListPurchaseDetails::route('/'),
            'create' => Pages\CreatePurchaseDetail::route('/create'),
            'edit' => Pages\EditPurchaseDetail::route('/{record}/edit'),
        ];
    }
}

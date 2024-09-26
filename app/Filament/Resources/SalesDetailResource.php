<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesDetailResource\Pages;
use App\Filament\Resources\SalesDetailResource\RelationManagers;
use App\Models\Sale;
use App\Models\SaleDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SalesDetailResource extends Resource
{
    protected static ?string $model = SaleDetail::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    public static function shouldRegisterNavigation(): bool
    {
        return Sale::exists();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
            'index' => Pages\ListSalesDetails::route('/'),
            'create' => Pages\CreateSalesDetail::route('/create'),
            'edit' => Pages\EditSalesDetail::route('/{record}/edit'),
        ];
    }
}

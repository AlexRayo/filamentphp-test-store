<?php

namespace App\Filament\Resources\SalesDetailResource\Pages;

use App\Filament\Resources\SalesDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalesDetail extends EditRecord
{
    protected static string $resource = SalesDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

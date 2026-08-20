<?php

namespace App\Filament\Resources\ShippingReturnResource\Pages;

use App\Filament\Resources\ShippingReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShippingReturn extends EditRecord
{
    protected static string $resource = ShippingReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

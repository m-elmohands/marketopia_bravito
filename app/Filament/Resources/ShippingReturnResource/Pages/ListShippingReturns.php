<?php

namespace App\Filament\Resources\ShippingReturnResource\Pages;

use App\Filament\Resources\ShippingReturnResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShippingReturns extends ListRecords
{
    protected static string $resource = ShippingReturnResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

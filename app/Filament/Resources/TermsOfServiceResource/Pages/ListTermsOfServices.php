<?php

namespace App\Filament\Resources\TermsOfServiceResource\Pages;

use App\Filament\Resources\TermsOfServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTermsOfServices extends ListRecords
{
    protected static string $resource = TermsOfServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\TermsOfServiceResource\Pages;

use App\Filament\Resources\TermsOfServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTermsOfService extends EditRecord
{
    protected static string $resource = TermsOfServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

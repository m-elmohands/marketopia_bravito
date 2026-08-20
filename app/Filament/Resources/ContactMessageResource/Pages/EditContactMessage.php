<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactMessage extends EditRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('mark_as_read')
                ->label(__('filament.actions.mark_as_read'))
                ->icon('heroicon-o-envelope-open')
                ->color('success')
                ->action(fn () => $this->record->markAsRead())
                ->requiresConfirmation()
                ->visible(fn (): bool => !$this->record->is_read),

            Actions\Action::make('mark_as_unread')
                ->label(__('filament.actions.mark_as_unread'))
                ->icon('heroicon-o-envelope')
                ->color('warning')
                ->action(fn () => $this->record->markAsUnread())
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->is_read),

            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Mark as read when editing if not already read
        if (!$this->record->is_read) {
            $data['is_read'] = true;
            $data['read_at'] = now();
        }

        return $data;
    }
}

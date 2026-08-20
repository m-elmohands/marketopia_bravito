<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Communications';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) ContactMessage::unread()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $unreadCount = ContactMessage::unread()->count();
        return $unreadCount > 0 ? 'danger' : 'success';
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.models.contact_messages');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.contact_message');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.contact_messages');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.contact_message_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament.fields.name'))
                            ->required()
                            ->disabled()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label(__('filament.fields.email'))
                            ->email()
                            ->required()
                            ->disabled()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone')
                            ->label(__('filament.fields.phone'))
                            ->disabled()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('message')
                            ->label(__('filament.fields.message'))
                            ->required()
                            ->disabled()
                            ->rows(5)
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('created_at')
                            ->label(__('filament.fields.created_at'))
                            ->content(fn (ContactMessage $record): string => $record->created_at->format('Y-m-d H:i:s')),

                        Forms\Components\Placeholder::make('read_at')
                            ->label(__('filament.fields.read_at'))
                            ->content(fn (ContactMessage $record): string => $record->read_at ? $record->read_at->format('Y-m-d H:i:s') : '-')
                            ->visible(fn (ContactMessage $record): bool => $record->is_read),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('filament.sections.notes'))
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(__('filament.fields.notes'))
                            ->rows(4)
                            ->columnSpanFull()
                            ->disabled(fn ($livewire) => $livewire instanceof \App\Filament\Resources\ContactMessageResource\Pages\ViewContactMessage),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('filament.fields.id'))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_read')
                    ->label(__('filament.fields.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn (ContactMessage $record): string => 
                        $record->is_read 
                            ? __('filament.fields.read') . ($record->read_at ? ' - ' . $record->read_at->diffForHumans() : '')
                            : __('filament.fields.unread')
                    ),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.fields.name'))
                    ->sortable()
                    ->searchable()
                    ->weight(fn (ContactMessage $record) => $record->is_read ? 'normal' : 'bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament.fields.email'))
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->weight(fn (ContactMessage $record) => $record->is_read ? 'normal' : 'bold'),

                Tables\Columns\TextColumn::make('phone')
                    ->label(__('filament.fields.phone'))
                    ->sortable()
                    ->searchable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('message')
                    ->label(__('filament.fields.message'))
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 50 ? $state : null;
                    })
                    ->weight(fn (ContactMessage $record) => $record->is_read ? 'normal' : 'bold'),

                Tables\Columns\TextColumn::make('notes')
                    ->label(__('filament.fields.notes'))
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    })
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->defaultSort('is_read', 'asc')
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('filament.filters.date_from')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('filament.filters.date_to')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('email')
                    ->label(__('filament.filters.email'))
                    ->options(function () {
                        return ContactMessage::query()
                            ->select('email')
                            ->distinct()
                            ->pluck('email', 'email')
                            ->toArray();
                    }),

                Tables\Filters\TernaryFilter::make('is_read')
                    ->label(__('filament.filters.read_status'))
                    ->placeholder(__('filament.filters.all_messages'))
                    ->trueLabel(__('filament.filters.read_only'))
                    ->falseLabel(__('filament.filters.unread_only'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('is_read', true),
                        false: fn (Builder $query) => $query->where('is_read', false),
                    ),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_as_read')
                    ->label(__('filament.actions.mark_as_read'))
                    ->icon('heroicon-o-envelope-open')
                    ->color('success')
                    ->action(fn (ContactMessage $record) => $record->markAsRead())
                    ->requiresConfirmation()
                    ->visible(fn (ContactMessage $record): bool => !$record->is_read),

                Tables\Actions\Action::make('mark_as_unread')
                    ->label(__('filament.actions.mark_as_unread'))
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->action(fn (ContactMessage $record) => $record->markAsUnread())
                    ->requiresConfirmation()
                    ->visible(fn (ContactMessage $record): bool => $record->is_read),

                Tables\Actions\ViewAction::make()
                    ->before(function (ContactMessage $record) {
                        if (!$record->is_read) {
                            $record->markAsRead();
                        }
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_as_read')
                        ->label(__('filament.actions.mark_as_read'))
                        ->icon('heroicon-o-envelope-open')
                        ->color('success')
                        ->action(fn ($records) => $records->each->markAsRead())
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('mark_as_unread')
                        ->label(__('filament.actions.mark_as_unread'))
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->action(fn ($records) => $records->each->markAsUnread())
                        ->deselectRecordsAfterCompletion()
                        ->requiresConfirmation(),

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
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}

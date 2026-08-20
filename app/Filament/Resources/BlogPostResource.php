<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 2;

    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationGroup(): string
    {
        return __('filament.navigation_groups.content_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.models.blog_posts');
    }

    public static function getModelLabel(): string
    {
        return __('filament.models.blog_post');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.models.blog_posts');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.blog_post_information'))
                    ->schema([
                        Forms\Components\Tabs::make('Translations')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('English')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_en')
                                            ->label(__('filament.fields.title') . ' (EN)')
                                            ->required()
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set, ?string $state) {
                                                if (!$get('slug') && $state) {
                                                    $set('slug', Str::slug($state));
                                                }
                                            })
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('excerpt_en')
                                            ->label(__('filament.fields.excerpt') . ' (EN)')
                                            ->rows(3),
                                        Forms\Components\RichEditor::make('content_en')
                                            ->label(__('filament.fields.content') . ' (EN)')
                                            ->required()
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'link',
                                                'bulletList',
                                                'orderedList',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Arabic')
                                    ->schema([
                                        Forms\Components\TextInput::make('title_ar')
                                            ->label(__('filament.fields.title') . ' (AR)')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('excerpt_ar')
                                            ->label(__('filament.fields.excerpt') . ' (AR)')
                                            ->rows(3),
                                        Forms\Components\RichEditor::make('content_ar')
                                            ->label(__('filament.fields.content') . ' (AR)')
                                            ->required()
                                            ->toolbarButtons([
                                                'bold',
                                                'italic',
                                                'link',
                                                'bulletList',
                                                'orderedList',
                                            ])
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('slug')
                            ->label(__('filament.fields.slug'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('featured_image')
                            ->label(__('filament.fields.featured_image'))
                            ->image()
                            ->directory('blog')
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('category_id')
                                    ->label(__('filament.fields.category'))
                                    ->relationship('category', app()->getLocale() == 'ar' ? 'name_ar' : 'name_en')
                                    ->searchable()
                                    ->required()
                                    ->preload(),
                                Forms\Components\Select::make('user_id')
                                    ->label(__('filament.fields.author'))
                                    ->relationship('author', 'name')
                                    ->searchable()
                                    ->default(fn () => auth()->id())
                                    ->required()
                                    ->preload(),
                            ]),
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label(__('filament.fields.published_at'))
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('is_featured')
                                    ->label(__('filament.fields.is_featured'))
                                    ->default(false),
                                Forms\Components\Toggle::make('is_active')
                                    ->label(__('filament.fields.is_active'))
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('filament.fields.id'))
                    ->sortable(),
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label(__('filament.fields.featured_image')),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.fields.title'))
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('category.' . (app()->getLocale() == 'ar' ? 'name_ar' : 'name_en'))
                    ->label(__('filament.fields.category'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label(__('filament.fields.author'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label(__('filament.fields.views_count'))
                    ->sortable(),
                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label(__('filament.fields.is_featured')),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label(__('filament.fields.is_active')),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('filament.fields.published_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(__('filament.filters.category'))
                    ->relationship('category', app()->getLocale() == 'ar' ? 'name_ar' : 'name_en'),
                Tables\Filters\SelectFilter::make('author')
                    ->label(__('filament.filters.author'))
                    ->relationship('author', 'name'),
                Tables\Filters\Filter::make('published')
                    ->label(__('filament.filters.published'))
                    ->query(fn ($query) => $query->published()),
                Tables\Filters\Filter::make('featured')
                    ->label(__('filament.filters.featured'))
                    ->query(fn ($query) => $query->where('is_featured', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}


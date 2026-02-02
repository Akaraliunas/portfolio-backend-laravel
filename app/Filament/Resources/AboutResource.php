<?php

namespace App\Filament\Resources;

use App\Models\About;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;

class AboutResource extends Resource
{
    protected static ?string $model = About::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('full_name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Senior Magento Developer'),

                        Forms\Components\MarkdownEditor::make('bio')
                            ->required()
                            ->columnSpanFull()
                            ->placeholder('Write your professional bio...'),

                        Forms\Components\FileUpload::make('profile_image')
                            ->image()
                            ->disk('public')
                            ->directory('about')
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),

                Section::make('Links & Resources')
                    ->schema([
                        Forms\Components\TextInput::make('cv_link')
                            ->url()
                            ->placeholder('https://example.com/cv.pdf'),

                        Forms\Components\Repeater::make('social_links')
                            ->addActionLabel('Add Social Link')
                            ->schema([
                                Forms\Components\TextInput::make('platform')
                                    ->required()
                                    ->placeholder('twitter, github, linkedin, etc.'),
                                Forms\Components\TextInput::make('url')
                                    ->required()
                                    ->url()
                                    ->placeholder('https://...'),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ])
            ->paginated(false);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\AboutResource\Pages\ListAbouts::route('/'),
            'edit' => \App\Filament\Resources\AboutResource\Pages\EditAbout::route('/{record}/edit'),
        ];
    }
}

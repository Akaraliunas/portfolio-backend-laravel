<?php

namespace App\Filament\Resources;

use App\Models\Experience;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;

class ExperienceResource extends Resource
{
    protected static ?string $model = Experience::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Experience Details')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('role')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Senior Developer'),

                        Forms\Components\TextInput::make('period')
                            ->required()
                            ->placeholder('e.g., 2023.07 - Now')
                            ->helperText('Format: YYYY.MM - YYYY.MM or YYYY.MM - Now'),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull()
                            ->rows(4),

                        Forms\Components\Repeater::make('technologies')
                            ->label('Technologies')
                            ->addActionLabel('Add Technology')
                            ->schema([
                                Forms\Components\TextInput::make('value')
                                    ->placeholder('e.g., Laravel, GraphQL, Vue.js')
                                    ->required(),
                            ])
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->helperText('Lower numbers appear first'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->searchable(),
                Tables\Columns\TextColumn::make('period')
                    ->sortable(),
                Tables\Columns\TextColumn::make('order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('order', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ExperienceResource\Pages\ListExperiences::route('/'),
            'create' => \App\Filament\Resources\ExperienceResource\Pages\CreateExperience::route('/create'),
            'edit' => \App\Filament\Resources\ExperienceResource\Pages\EditExperience::route('/{record}/edit'),
        ];
    }
}

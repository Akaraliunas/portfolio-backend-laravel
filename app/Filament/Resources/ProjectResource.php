<?php

namespace App\Filament\Resources;

use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Project Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull()
                            ->rows(4),

                        Forms\Components\FileUpload::make('thumbnail')
                            ->image()
                            ->disk('public')
                            ->directory('projects')
                            ->visibility('public')
                            ->columnSpanFull(),

                        Forms\Components\Repeater::make('tech_stack')
                            ->label('Technologies')
                            ->addActionLabel('Add Technology')
                            ->schema([
                                Forms\Components\TextInput::make('value')
                                    ->placeholder('e.g., Laravel, Vue.js, GraphQL')
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Links')
                    ->schema([
                        Forms\Components\TextInput::make('github_link')
                            ->url()
                            ->placeholder('https://github.com/...'),

                        Forms\Components\TextInput::make('live_link')
                            ->url()
                            ->placeholder('https://example.com'),
                    ]),

                Section::make('Ordering')
                    ->schema([
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
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50),
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
            'index' => \App\Filament\Resources\ProjectResource\Pages\ListProjects::route('/'),
            'create' => \App\Filament\Resources\ProjectResource\Pages\CreateProject::route('/create'),
            'edit' => \App\Filament\Resources\ProjectResource\Pages\EditProject::route('/{record}/edit'),
        ];
    }
}

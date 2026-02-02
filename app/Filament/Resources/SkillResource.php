<?php

namespace App\Filament\Resources;

use App\Models\Skill;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;

class SkillResource extends Resource
{
    protected static ?string $model = Skill::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    protected static ?string $navigationGroup = 'Portfolio';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Skill Information')
                    ->schema([
                        Forms\Components\TextInput::make('category')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Magento, GraphQL, Backend'),

                        Forms\Components\TextInput::make('icon')
                            ->maxLength(255)
                            ->placeholder('SVG path or icon identifier')
                            ->helperText('Leave empty to use file upload below'),

                        Forms\Components\FileUpload::make('icon_file')
                            ->image()
                            ->disk('public')
                            ->directory('skills')
                            ->visibility('public')
                            ->label('Icon File')
                            ->helperText('Upload SVG or image icon'),

                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull()
                            ->rows(3),

                        Forms\Components\Repeater::make('sub_skills')
                            ->label('Sub Skills / Technologies')
                            ->addActionLabel('Add Sub Skill')
                            ->schema([
                                Forms\Components\TextInput::make('value')
                                    ->placeholder('e.g., Apollo, Schema Design, REST APIs')
                                    ->required(),
                            ])
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('order')
                            ->numeric()
                            ->required()
                            ->default(0)
                            ->helperText('Sort order within category'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
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
                Tables\Filters\SelectFilter::make('category')
                    ->options(
                        Skill::distinct()
                            ->pluck('category')
                            ->mapWithKeys(fn($cat) => [$cat => $cat])
                            ->toArray()
                    ),
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
            ->defaultSort('category', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\SkillResource\Pages\ListSkills::route('/'),
            'create' => \App\Filament\Resources\SkillResource\Pages\CreateSkill::route('/create'),
            'edit' => \App\Filament\Resources\SkillResource\Pages\EditSkill::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AboutResource\Pages;
use App\Filament\Resources\AboutResource\RelationManagers;
use App\Models\About;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AboutResource extends Resource
{
    protected static ?string $model = About::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
           ->schema([
            Forms\Components\Section::make('Personal Information')
                ->schema([
                    Forms\Components\TextInput::make('full_name')
                        ->required(),
                    Forms\Components\TextInput::make('title')
                        ->placeholder('e.g. Full-stack Developer')
                        ->required(),
                    Forms\Components\RichEditor::make('bio') // RichEditor is better for bios
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Assets')
                ->schema([
                    Forms\Components\FileUpload::make('profile_image')
                        ->image()
                        ->directory('about')
                        ->imageEditor(), // Allows you to crop your profile pic
                    Forms\Components\FileUpload::make('cv_link')
                        ->label('CV / Resume (PDF)')
                        ->directory('cvs')
                        ->acceptedFileTypes(['application/pdf']),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_image')
                    ->circular(),
                Tables\Columns\TextColumn::make('full_name'),
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListAbouts::route('/'),
            'create' => Pages\CreateAbout::route('/create'),
            'edit' => Pages\EditAbout::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
    return About::count() < 1; // Only allow creation if the table is empty
    }

    public static function canDeleteAny(): bool
    {
        return false; // Prevent bulk deletion
    }
}

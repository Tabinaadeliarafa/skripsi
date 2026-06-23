<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporanBencanaResource\Pages;
use App\Filament\Resources\LaporanBencanaResource\RelationManagers;
use App\Models\LaporanBencana;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LaporanBencanaResource extends Resource
{
    protected static ?string $model = LaporanBencana::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('desa_id')
                    ->relationship('desa', 'name')
                    ->required(),
                Forms\Components\Select::make('jenis_bencana_id')
                    ->relationship('jenisBencana', 'name')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('latitude')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('longitude')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'resolved' => 'Resolved',
                    ])
                    ->required()
                    ->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('desa.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenisBencana.name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListLaporanBencanas::route('/'),
            'create' => Pages\CreateLaporanBencana::route('/create'),
            'edit' => Pages\EditLaporanBencana::route('/{record}/edit'),
        ];
    }
}

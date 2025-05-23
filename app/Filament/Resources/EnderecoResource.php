<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnderecoResource\Pages;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

use App\Models\Endereco;
use App\Models\Enums\EstadoEnum;

class EnderecoResource extends Resource
{
    protected static ?string $model = Endereco::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('cep')->numeric()->length(8)->autofocus()->required(),
                TextInput::make('logradouro')->required(),
                TextInput::make('bairro')->required(),
                TextInput::make('cidade')->required(),
                Select::make('uf')->label('Estado')->options(
                    EstadoEnum::class
                )->required(),
                TextInput::make('numero')->numeric(),
                TextInput::make('complemento')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('logradouro')
                    ->searchable(),
                TextColumn::make('bairro')
                    ->searchable(),
                TextColumn::make('cidade')
                    ->searchable(),
                TextColumn::make('uf')
                    ->label('Estado')
                    ->searchable(),
                TextColumn::make('numero')
                    ->searchable(),
                TextColumn::make('complemento')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListEnderecos::route('/'),
            'create' => Pages\CreateEndereco::route('/create'),
        ];
    }
}

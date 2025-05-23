<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventoResource\Pages;
use App\Models\Endereco;
use App\Models\Evento;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventoResource extends Resource
{
    protected static ?string $model = Evento::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // TODO: Não estava gravando o endereço
                // Section::make('Endereço')->relationship('endereco')->columns(2)->schema([
                //     TextInput::make('cep')->numeric()->length(8)->required(),
                //     TextInput::make('logradouro')->required(),
                //     TextInput::make('bairro')->required(),
                //     TextInput::make('cidade')->required(),
                //     TextInput::make('uf')->required(),
                //     TextInput::make('numero')->numeric(),
                //     TextInput::make('complemento')
                // ]),

                TextInput::make('titulo')->label('Título')->autofocus()->required(),
                TextInput::make('descricao')->label('Descrição')->required(),
                TextInput::make('capacidade')->numeric()->required(),
                TextInput::make('idade_min')->label('Idade minima')->numeric()->placeholder('Deixar vazio, caso seja livre para todas as faixas etárias!'),
                TextInput::make('preco')->label('Preço')->numeric()->required(),
                DateTimePicker::make('dt_evento')->label('Data do Evento')
                    ->displayFormat('F j, Y H:i')
                    ->firstDayOfWeek(1)
                    ->format('Y-m-d H:i')
                    ->minDate(now()->setSeconds(0))
                    ->seconds(false)
                    ->required(),
                Select::make('endereco_id')->label('Endereço')->relationship('endereco', 'id')
                    ->getOptionLabelFromRecordUsing(fn (Endereco $record): string => (String) $record)
                    ->createOptionForm(fn (Form $form) => EnderecoResource::form($form)->columns(2))
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('titulo')
                    ->label('Título')
                    ->description(fn (Evento $record): String => $record->descricao)
                    ->wrap()
                    ->searchable([
                        'titulo',
                        'descricao'
                    ]),
                TextColumn::make('capacidade')
                    ->label('Capacidade'),
                TextColumn::make('idade_min')
                    ->label('Idade Min.'),
                TextColumn::make('preco')
                    ->label('Preço'),
                TextColumn::make('dt_evento')
                    ->label('Data do Evento')
                    ->datetime('d/m/Y \à\s H:i'),
                TextColumn::make('dt_cancelamento')
                    ->label('Cancelado em')
                    ->datetime('d/m/Y \à\s H:i')
                    ->badge()
                    ->color('danger'),
                TextColumn::make('endereco.display_name')
                    ->label('Endereço')
                    ->searchable([
                        'logradouro',
                        'bairro',
                        'cidade',
                        'uf',
                    ])
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
            'index' => Pages\ListEventos::route('/'),
            'create' => Pages\CreateEvento::route('/create'),
            'edit' => Pages\EditEvento::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\EventoResource\Pages;

use App\Filament\Resources\EventoResource;
use App\Models\Enums\PermissaoEnum;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewEvento extends ViewRecord
{
    protected static string $resource = EventoResource::class;

    public static function canView(Model $record): bool
    {
        $user = auth()->user();
        $permissao = $user->permissao->role;

        if ($permissao == PermissaoEnum::ORGANIZADOR) {
            return $record->organizador_id == $user->id;
        }

        return true;
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

use App\Models\Enums\PermissaoEnum;
use DateTime;

class Usuario extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'dt_nascimento',
        'name',
        'email',
        'password',
        'permissao_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'permissao_id',
        'password',
        'remember_token',
    ];

    function permissao () {
        return $this->belongsTo(Permissao::class, 'permissao_id');
    }

    function getIdadeAttribute(): int {
        // Função que calcula a idade

        $dt_nascimento = date_create_from_format('Y-m-d', $this->dt_nascimento);

        $idade = $dt_nascimento
            ->diff(new DateTime('now'))
            ->y;
        return $idade;
    }

    public function isFilamentAdmin(): bool
    {
        return $this->permissao->role == PermissaoEnum::ADMINISTRADOR;
    }

    public function canAccessFilament (): bool {
        return $this->isFilamentAdmin();
    }

    public function canAccessPanel(Panel $panel): bool {
        return $this->isFilamentAdmin();
    }

    protected static function booted() {
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser; // Pridėk šitą
use Filament\Panel;                         // Pridėk šitą
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser // Pridėk "implements FilamentUser"
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Pridėk šį metodą
    public function canAccessPanel(Panel $panel): bool
    {
        // Kadangi tai tavo asmeninis portfolio,
        // gali tiesiog grąžinti true arba patikrinti el. paštą:
        return str_ends_with($this->email, '@karaliunas.dev');
    }
}

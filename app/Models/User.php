<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'password',
        'role',
        'actif',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'actif'             => 'boolean',
    ];

    // ==================== RELATIONS ====================

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function mouvementsStock()
    {
        return $this->hasMany(MouvementStock::class);
    }

    public function inventaires()
    {
        return $this->hasMany(Inventaire::class);
    }

    public function alertesTraitees()
    {
        return $this->hasMany(Alerte::class, 'traitee_par');
    }

    // ==================== HELPERS ====================

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCaissier(): bool
    {
        return $this->role === 'caissier';
    }

    public function isSuperviseur(): bool
    {
        return $this->role === 'superviseur';
    }

    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin'       => 'Administrateur',
            'caissier'    => 'Caissier',
            'superviseur' => 'Superviseur',
            default       => 'Inconnu',
        };
    }

    public function getRoleBadgeAttribute(): string
    {
        return match($this->role) {
            'admin'       => 'badge-danger',
            'caissier'    => 'badge-success',
            'superviseur' => 'badge-warning',
            default       => 'badge-secondary',
        };
    }
}
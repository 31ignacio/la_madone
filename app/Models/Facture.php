<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'numero',
        'client_nom','client_id',
        'client_telephone',
        'statut',
        'mode_paiement',
        'sous_total',
        'remise',
        'total',
        'montant_recu',
        'monnaie',
        'notes','reste_a_payer',
    ];

    protected $casts = [
        'sous_total'    => 'decimal:2',
        'remise'        => 'decimal:2',
        'total'         => 'decimal:2',
        'montant_recu'  => 'decimal:2',
        'monnaie'       => 'decimal:2',
    ];

    // ==================== RELATIONS ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lignes()
    {
        return $this->hasMany(FactureLigne::class);
    }

    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }

    // ==================== HELPERS ====================

    public static function genererNumero(): string
    {
        $prefix  = 'F-' . date('Y') . '-';
        $dernier = self::where('numero', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        $numero = $dernier
            ? (int) substr($dernier->numero, strlen($prefix)) + 1
            : 1;

        return $prefix . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }

    public function getStatutBadgeAttribute(): string
    {
        return match($this->statut) {
            'en_cours' => 'badge-warning',
            'payee'    => 'badge-success',
            'annulee'  => 'badge-danger',
            default    => 'badge-secondary',
        };
    }

    public function getStatutLabelAttribute(): string
    {
        return match($this->statut) {
            'en_cours' => 'En cours',
            'payee'    => 'Payée',
            'annulee'  => 'Annulée',
            default    => 'Inconnu',
        };
    }

    public function getModePaiementLabelAttribute(): string
    {
        return match($this->mode_paiement) {
            'espece'       => 'Espèce',
            'carte'        => 'Carte bancaire',
            'mobile_money' => 'Mobile Money',
            'credit'       => 'Crédit',
            default        => 'Inconnu',
        };
    }

    public function getTypePrixLabelAttribute(): string
    {
        return match($this->type_prix) {
            'detail' => 'Détail',
            'moyen'  => 'Moyen',
            'gros'   => 'Gros',
            default  => 'Inconnu',
        };
    }

    // app/Models/Facture.php — ajouter ces méthodes

    public function reglements()
    {
        return $this->hasMany(Reglement::class);
    }
    public function client()
{
    return $this->belongsTo(Client::class);
}

    // ✅ Toujours requête fraîche BDD
    public function getMontantPayeAttribute()
    {
        return $this->reglements()->sum('montant');
    }

    public function getResteAPayerAttribute()
    {
        return max($this->total - $this->reglements()->sum('montant'), 0);
    }

    public function getPourcentagePayeAttribute()
    {
        if ($this->total <= 0) return 100;
        $pct = ($this->reglements()->sum('montant') / $this->total) * 100;
        return min(round($pct), 100);
    }

    public function isEntierementRegle()
    {
        return $this->getResteAPayerAttribute() <= 0;
    }
}
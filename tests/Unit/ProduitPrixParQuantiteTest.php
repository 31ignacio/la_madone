<?php

namespace Tests\Unit;

use App\Models\Produit;
use PHPUnit\Framework\TestCase;

class ProduitPrixParQuantiteTest extends TestCase
{
    public function test_it_applies_the_configured_price_tiers(): void
    {
        $produit = new Produit([
            'prix_detail' => 100,
            'seuil_detail' => 2,
            'prix_moyen' => 90,
            'seuil_moyen' => 10,
            'prix_gros' => 80,
        ]);

        $this->assertSame(100.0, $produit->getPrixParQuantite(2));
        $this->assertSame(90.0, $produit->getPrixParQuantite(3));
        $this->assertSame(80.0, $produit->getPrixParQuantite(11));
    }

    public function test_it_uses_the_gros_price_without_an_intermediate_tier(): void
    {
        $produit = new Produit([
            'prix_detail' => 100,
            'seuil_detail' => 2,
            'prix_gros' => 80,
        ]);

        $this->assertSame(100.0, $produit->getPrixParQuantite(2));
        $this->assertSame(80.0, $produit->getPrixParQuantite(3));
    }
}

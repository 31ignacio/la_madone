<?php
// app/Http/Controllers/ClientController.php
namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::withCount('factures');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom',       'like', "%$s%")
                  ->orWhere('prenom',  'like', "%$s%")
                  ->orWhere('telephone','like', "%$s%")
                  ->orWhere('email',   'like', "%$s%")
                  ->orWhere('ifu',     'like', "%$s%");   // recherche par IFU aussi
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('actif', $request->statut === 'actif');
        }

        $clients = $query->orderBy('nom')->paginate(20)->withQueryString();

        $stats = [
            'total'       => Client::count(),
            'actifs'      => Client::where('actif', true)->count(),
            'entreprises' => Client::where('type', 'entreprise')->count(),
            'creances'    => Client::join('factures', 'clients.id', '=', 'factures.client_id')
                                   ->where('factures.statut', 'en_cours')
                                   ->where('factures.mode_paiement', 'credit')
                                   ->sum('factures.reste_a_payer'),
        ];

        return view('clients.index', compact('clients', 'stats'));
    }

    public function create()
    {
        return view('clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'nullable|string|max:100',
            'type'      => 'required|in:particulier,entreprise',
            'telephone' => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:150',
            'adresse'   => 'nullable|string|max:255',
            'ifu'       => 'nullable|string|digits:13|unique:clients,ifu',   // ← IFU : 13 chiffres, unique
            'actif'     => 'nullable|boolean',
        ]);

        $validated['actif'] = $request->boolean('actif', true);

        $client = Client::create($validated);

        // Si requête Ajax (depuis la caisse) → retourner JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'client'  => $client,
            ]);
        }

        // Sinon comportement normal → redirect
        return redirect()->route('clients.show', $client)
            ->with('success', "Client « {$client->nom_complet} » créé avec succès.");
    }

    public function show(Client $client)
    {
        $client->loadCount('factures');

        $factures = $client->factures()
            ->with('user')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_depense'  => $client->factures()->where('statut', 'payee')->sum('total'),
            'nb_factures'    => $client->factures()->count(),
            'creances'       => $client->factures()
                                       ->where('statut', 'en_cours')
                                       ->where('mode_paiement', 'credit')
                                       ->sum('reste_a_payer'),
            'derniere_visite'=> $client->factures()->latest()->value('created_at'),
        ];

        return view('clients.show', compact('client', 'factures', 'stats'));
    }

    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'nom'       => 'required|string|max:100',
            'prenom'    => 'nullable|string|max:100',
            'type'      => 'required|in:particulier,entreprise',
            'telephone' => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:150',
            'adresse'   => 'nullable|string|max:255',
            'ifu'       => 'nullable|string|digits:13|unique:clients,ifu,' . $client->id,   // ← ignore le client actuel
            'actif'     => 'nullable|boolean',
        ]);

        $validated['actif'] = $request->boolean('actif');

        $client->update($validated);

        return redirect()->route('clients.show', $client)
            ->with('success', "Client « {$client->nom_complet} » mis à jour.");
    }

    public function destroy(Client $client)
    {
        // Détacher les factures avant suppression
        $client->factures()->update(['client_id' => null]);
        $client->delete();

        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé.');
    }

    public function toggleActif(Client $client)
    {
        $client->update(['actif' => !$client->actif]);
        $msg = $client->actif ? 'activé' : 'désactivé';
        return back()->with('success', "Client {$msg}.");
    }

    // Recherche AJAX pour la caisse
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $clients = Client::where('actif', true)
            ->where(function ($query) use ($q) {
                $query->where('nom',       'like', "%$q%")
                      ->orWhere('prenom',  'like', "%$q%")
                      ->orWhere('telephone','like', "%$q%")
                      ->orWhere('ifu',     'like', "%$q%");  // recherche par IFU depuis la caisse
            })
            ->select('id', 'nom', 'prenom', 'telephone', 'type', 'solde_credit', 'ifu')
            ->limit(8)
            ->get()
            ->map(fn($c) => [
                'id'           => $c->id,
                'nom_complet'  => $c->nom_complet,
                'telephone'    => $c->telephone,
                'type'         => $c->type,
                'type_label'   => $c->type_label,
                'solde_credit' => (float) $c->solde_credit,
                'ifu'          => $c->ifu,
            ]);

        return response()->json($clients);
    }
}
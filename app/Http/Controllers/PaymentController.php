<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Enregistrer un paiement "Marquer payé"
    public function store(Request $request, Colocation $colocation)
    {
        // Vérifier que l'user est membre
        $membership = $colocation->activeMembers()
            ->where('user_id', auth()->id())
            ->first();

        if (!$membership) {
            abort(403);
        }

        $request->validate([
            'to_user_id' => ['required', 'exists:users,id'],
            'amount'=> ['required', 'numeric', 'min:0.01'],
        ]);

        Payment::create([
            'colocation_id' => $colocation->id,
            'from_user_id'  => auth()->id(),
            'to_user_id' => $request->to_user_id,
            'amount' => $request->amount,
            'paid_at' => now(),
        ]);

        return redirect()->route('colocations.show', $colocation)->with('success', 'Paiement enregistré avec succès !');
    }

    // Historique des paiements
    public function index(Colocation $colocation)
    {
        $membership = $colocation->activeMembers()
            ->where('user_id', auth()->id())
            ->first();

        if (!$membership) {
            abort(403);
        }

        $payments = Payment::where('colocation_id', $colocation->id)
            ->with('fromUser', 'toUser')
            ->orderBy('paid_at', 'desc')
            ->get();

        return view('payments.index', compact('colocation', 'payments'));
    }
}
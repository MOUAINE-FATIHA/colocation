<?php

namespace App\Http\Controllers;
use App\Models\Colocation;
use App\Models\Membership;
use Illuminate\Http\Request;

class ColocationController extends Controller
{
    public function create()
    {
        return view('colocations.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        // bloquer si user a deja une colocation 
        if (auth()->user()->activeMembership) {
            return back()->withErrors(['name' => 'Vous avez déjà une colocation active.']);
        }

        $colocation = Colocation::create([
            'name'   => $request->name,
            'status' => 'active',
        ]);

        Membership::create([
            'user_id'       => auth()->id(),
            'colocation_id' => $colocation->id,
            'role'          => 'owner',
            'joined_at'     => now(),
        ]);

        return redirect()->route('colocations.show', $colocation)
            ->with('success', 'Colocation créée avec succès !');
    }

    public function show(Colocation $colocation)
    {
        $membership = $colocation->activeMembers()
            ->where('user_id', auth()->id())
            ->first();

        if (!$membership) {
            abort(403, 'Vous n\'êtes pas membre de cette colocation.');
        }

        $members = $colocation->activeMembers()->with('user')->get();
        return view('colocations.show', compact('colocation', 'members', 'membership'));
    }

    public function cancel(Colocation $colocation)
    {
        $membership = $colocation->activeMembers()
            ->where('user_id', auth()->id())
            ->where('role', 'owner')
            ->first();

        if (!$membership) {
            abort(403, 'Seul le propriétaire peut annuler la colocation.');
        }

        $colocation->update(['status' => 'cancelled']);
        $colocation->activeMembers()->update(['left_at' => now()]);

        return redirect()->route('dashboard')
            ->with('success', 'Colocation annulée.');
    }

    public function leave(Colocation $colocation)
    {
        $membership = $colocation->activeMembers()
            ->where('user_id', auth()->id())
            ->first();

        if (!$membership) {
            abort(403);
        }
        if ($membership->role === 'owner') {
            return back()->withErrors(['error' => 'Le propriétaire ne peut pas quitter la colocation. Annulez-la à la place.']);
        }
        $membership->update(['left_at' => now()]);
        return redirect()->route('dashboard')
            ->with('success', 'Vous avez quitté la colocation.');
    }
}
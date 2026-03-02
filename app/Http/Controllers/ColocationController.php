<?php

namespace App\Http\Controllers;
use App\Models\Colocation;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Models\User;

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
            'name'=> $request->name,
            'status' => 'active',
        ]);

        Membership::create([
            'user_id' => auth()->id(),
            'colocation_id' => $colocation->id,
            'role' => 'owner',
            'joined_at' => now(),
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
            abort(403, 'Vous n\'etes pas membre de cette colocation.');
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
        $colocation->activeMembers()->with('user')->get()->each(function ($m) use ($colocation) {
            $this->adjustReputation($m->user, $colocation);
        });

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
        $this->adjustReputation(auth()->user(), $colocation);

        $membership->update(['left_at' => now()]);

        return redirect()->route('dashboard')
            ->with('success', 'Vous avez quitté la colocation.');
    }

    // retirer un membre
    public function removeMember(Colocation $colocation, User $user)
    {
        $ownerMembership = $colocation->activeMembers()
            ->where('user_id', auth()->id())
            ->where('role', 'owner')
            ->first();

        if (!$ownerMembership) {
            abort(403, 'Seul le propriétaire peut retirer un membre.');
        }
        $memberMembership = $colocation->activeMembers()
            ->where('user_id', $user->id)
            ->first();

        if (!$memberMembership || $memberMembership->role === 'owner') {
            abort(403, 'Impossible de retirer ce membre.');
        }
        $this->adjustReputation($user, $colocation);
        $memberMembership->update(['left_at' => now()]);
        return back()->with('success', $user->name . ' a été retiré de la colocation.');
    }

    private function adjustReputation(User $user, Colocation $colocation): void
    {
        $expenses= $colocation->expenses;
        $memberCount  = $colocation->activeMembers()->count();
        if ($memberCount=== 0) return;

        $totalExpenses  = $expenses->sum('amount');
        $sharePerPerson = $totalExpenses / $memberCount;
        $paid = $expenses->where('paid_by', $user->id)->sum('amount');
        $balance  = $paid - $sharePerPerson;

        if ($balance < 0) {
            $user->decrement('reputation');
        } else {
            $user->increment('reputation');
        }
    }
}
<?php

namespace App\Http\Controllers;
use App\Models\Colocation;
use App\Models\Membership;
use Illuminate\Http\Request;
use App\Models\Payment;
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

    public function show(Colocation $colocation, Request $request)
    {
        $membership = $colocation->activeMembers()
            ->where('user_id', auth()->id())
            ->first();

        if (!$membership) {
            abort(403, 'Vous n\'êtes pas membre de cette colocation.');
        }

        $members    = $colocation->activeMembers()->with('user')->get();
        $categories = $colocation->categories;

        // Dépenses avec filtre par mois
        $query = $colocation->expenses()->with('payer', 'category');
        if ($request->filled('month')) {
            $query->whereMonth('date', date('m', strtotime($request->month)))
                ->whereYear('date', date('Y', strtotime($request->month)));
        }
        $expenses = $query->orderBy('date', 'desc')->get();

        // Balances
        $payments     = Payment::where('colocation_id', $colocation->id)->get();
        $totalMembers = $members->count();
        $balances     = [];
        $settlements  = [];

        if ($totalMembers > 0) {
            foreach ($members as $m) {
                $balances[$m->user->id] = [
                    'user'    => $m->user,
                    'paid'    => 0,
                    'share'   => 0,
                    'balance' => 0,
                ];
            }

            $totalExpenses  = $colocation->expenses->sum('amount');
            $sharePerPerson = $totalExpenses / $totalMembers;

            foreach ($colocation->expenses as $expense) {
                if (isset($balances[$expense->paid_by])) {
                    $balances[$expense->paid_by]['paid'] += $expense->amount;
                }
            }

            foreach ($payments as $payment) {
                if (isset($balances[$payment->from_user_id])) {
                    $balances[$payment->from_user_id]['paid'] += $payment->amount;
                }
                if (isset($balances[$payment->to_user_id])) {
                    $balances[$payment->to_user_id]['paid'] -= $payment->amount;
                }
            }

            foreach ($balances as &$data) {
                $data['share']   = $sharePerPerson;
                $data['balance'] = $data['paid'] - $sharePerPerson;
            }
            unset($data);

            $settlements = $this->calculateSettlements($balances);
        }

        // Historique paiements
        $paymentHistory = Payment::where('colocation_id', $colocation->id)
            ->with('fromUser', 'toUser')
            ->orderBy('paid_at', 'desc')
            ->get();

        return view('colocations.show', compact(
            'colocation', 'members', 'membership',
            'categories', 'expenses', 'request',
            'balances', 'settlements', 'paymentHistory'
        ));
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

    private function calculateSettlements(array $balances): array
{
    $settlements = [];
    $debtors     = [];
    $creditors   = [];

    foreach ($balances as $userId => $data) {
        $b = round($data['balance'], 2);
        if ($b < 0) {
            $debtors[$userId] = abs($b);
        } elseif ($b > 0) {
            $creditors[$userId] = $b;
        }
    }

    foreach ($debtors as $debtorId => $debtAmount) {
        foreach ($creditors as $creditorId => $creditAmount) {
            if ($debtAmount <= 0 || $creditAmount <= 0) continue;
            $amount        = min($debtAmount, $creditAmount);
            $settlements[] = [
                'from'   => $balances[$debtorId]['user'],
                'to'     => $balances[$creditorId]['user'],
                'amount' => round($amount, 2),
            ];
            $debtors[$debtorId]     -= $amount;
            $creditors[$creditorId] -= $amount;
            $debtAmount             -= $amount;
        }
    }

    return $settlements;
}
}
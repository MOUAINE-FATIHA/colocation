<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Payment;

class BalanceController extends Controller
{
    public function index(Colocation $colocation)
    {
        $membership = $colocation->activeMembers()
            ->where('user_id', auth()->id())
            ->first();

        if (!$membership) {
            abort(403);
        }

        $members  = $colocation->activeMembers()->with('user')->get();
        $expenses = $colocation->expenses;
        $payments = Payment::where('colocation_id', $colocation->id)->get();

        $totalMembers = $members->count();

        if ($totalMembers === 0) {
            return view('balances.index', [
                'colocation'  => $colocation,
                'balances'    => [],
                'settlements' => [],
            ]);
        }

        // Initialiser les balances
        $balances = [];
        foreach ($members as $m) {
            $balances[$m->user->id] = [
                'user'    => $m->user,
                'paid'    => 0,
                'share'   => 0,
                'balance' => 0,
            ];
        }
        $totalExpenses  = $expenses->sum('amount');
        $sharePerPerson = $totalMembers > 0 ? $totalExpenses / $totalMembers : 0;

        foreach ($expenses as $expense) {
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

        foreach ($balances as $userId => &$data) {
            $data['share']   = $sharePerPerson;
            $data['balance'] = $data['paid'] - $sharePerPerson;
        }
        unset($data);

        $settlements = $this->calculateSettlements($balances);

        return view('balances.index', compact('colocation', 'balances', 'settlements'));
    }

    // Algorithme de simplification des dettes
    private function calculateSettlements(array $balances): array
    {
        $settlements = [];

        // Séparer débiteurs (balance < 0) et créditeurs (balance > 0)
        $debtors   = [];
        $creditors = [];

        foreach ($balances as $userId => $data) {
            $b = round($data['balance'], 2);
            if ($b < 0) {
                $debtors[$userId]   = abs($b);
            } elseif ($b > 0) {
                $creditors[$userId] = $b;
            }
        }

        // Associer débiteurs et créditeurs
        foreach ($debtors as $debtorId => $debtAmount) {
            foreach ($creditors as $creditorId => $creditAmount) {
                if ($debtAmount <= 0 || $creditAmount <= 0) continue;

                $amount = min($debtAmount, $creditAmount);

                $settlements[] = [
                    'from'   => $balances[$debtorId]['user'],
                    'to'     => $balances[$creditorId]['user'],
                    'amount' => round($amount, 2),
                ];

                $debtors[$debtorId]    -= $amount;
                $creditors[$creditorId] -= $amount;
                $debtAmount             -= $amount;
            }
        }

        return $settlements;
    }
}
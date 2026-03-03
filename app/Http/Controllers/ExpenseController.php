<?php

namespace App\Http\Controllers;

use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    // Liste des dépenses avec filtre par mois
    public function index(Colocation $colocation, Request $request)
    {
        $this->authorizeMember($colocation);

        $categories = $colocation->categories;
        $query = $colocation->expenses()->with('payer', 'category');
        if ($request->filled('month')) {
            $query->whereMonth('date', date('m', strtotime($request->month)))
                  ->whereYear('date', date('Y', strtotime($request->month)));
        }

        $expenses = $query->orderBy('date', 'desc')->get();

        return view('expenses.index', compact('colocation', 'expenses', 'categories', 'request'));
    }

    // form d'ajout
    public function create(Colocation $colocation){
        $this->authorizeMember($colocation);
        $categories = $colocation->categories;
        $members= $colocation->activeMembers()->with('user')->get();
        return view('expenses.create',compact('colocation', 'categories', 'members'));
    }

    // Enregistrer une dépense
    public function store(Request $request, Colocation $colocation)
    {
        $this->authorizeMember($colocation);

        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'date'        => ['required', 'date'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'paid_by'     => ['required', 'exists:users,id'],
        ]);

        Expense::create([
            'colocation_id' => $colocation->id,
            'paid_by'       => $request->paid_by,
            'category_id'   => $request->category_id,
            'title'    => $request->title,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);
        return redirect()->route('colocations.show', $colocation)->with('success', 'Dépense ajoutée.');
    }

    public function destroy(Colocation $colocation, Expense $expense)
    {
        $this->authorizeMember($colocation);
        $expense->delete();
        return redirect()->route('colocations.show', $colocation)->with('success', 'Dépense supprimée.');
    }

    private function authorizeMember(Colocation $colocation)
    {
        $membership = $colocation->activeMembers()
            ->where('user_id', auth()->id())
            ->first();

        if (!$membership) {
            abort(403, 'Vous n\'êtes pas membre de cette colocation.');
        }
    }
}
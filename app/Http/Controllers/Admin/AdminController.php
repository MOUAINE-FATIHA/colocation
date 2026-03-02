<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Colocation;
use App\Models\Expense;
use App\Models\User;

class AdminController extends Controller
{
    // Dashboard avec statistiques
    public function index()
    {
        $stats = [
            'total_users'       => User::count(),
            'total_colocations' => Colocation::count(),
            'total_expenses'    => Expense::count(),
            'banned_users'      => User::where('is_banned', true)->count(),
        ];

        $users = User::orderBy('created_at', 'desc')->get();

        return view('admin.dashboard', compact('stats', 'users'));
    }

    // Bannir un utilisateur
    public function ban(User $user)
    {
        // Empêcher de bannir un autre admin
        if ($user->is_admin) {
            return back()->withErrors(['error' => 'Impossible de bannir un administrateur.']);
        }

        $user->update(['is_banned' => true]);

        return back()->with('success', $user->name . ' a été banni.');
    }

    // Débannir un utilisateur
    public function unban(User $user)
    {
        $user->update(['is_banned' => false]);

        return back()->with('success', $user->name . ' a été débanni.');
    }
}
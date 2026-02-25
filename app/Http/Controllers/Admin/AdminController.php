<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Colocation;
use App\Models\Expense;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_colocations' => Colocation::count(),
            'total_expenses' => Expense::count(),
            'banned_users'=> User::where('is_banned', true)->count(),
        ];

        return view('admin.dashboard',compact('stats'));
    }
}
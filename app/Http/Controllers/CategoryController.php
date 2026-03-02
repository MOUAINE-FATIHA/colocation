<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Colocation;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Afficher les catégories (owner seulement)
    public function index(Colocation $colocation)
    {
        $this->authorizeOwner($colocation);
        $categories = $colocation->categories;
        return view('categories.index', compact('colocation', 'categories'));
    }

    // Créer une catégorie
    public function store(Request $request, Colocation $colocation)
    {
        $this->authorizeOwner($colocation);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        Category::create([
            'name'          => $request->name,
            'colocation_id' => $colocation->id,
        ]);

        return back()->with('success', 'Catégorie ajoutée.');
    }

    // Supprimer une catégorie
    public function destroy(Colocation $colocation, Category $category)
    {
        $this->authorizeOwner($colocation);
        $category->delete();
        return back()->with('success', 'Catégorie supprimée.');
    }

    private function authorizeOwner(Colocation $colocation)
    {
        $membership = $colocation->activeMembers()
            ->where('user_id', auth()->id())
            ->where('role', 'owner')
            ->first();

        if (!$membership) {
            abort(403, 'Seul le propriétaire peut gérer les catégories.');
        }
    }
}
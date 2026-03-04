<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-xl">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-2xl shadow p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Catégories — {{ $colocation->name }}</h2>

                <form method="POST" action="{{ route('categories.store', $colocation) }}" class="flex gap-3 mb-6">
                    @csrf
                    <x-text-input name="name" type="text" class="flex-1 rounded-xl"
                        placeholder="Ex: Nourriture, Loyer..." required />
                    <button type="submit"
                        class="px-5 py-2 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition font-medium">
                        + Ajouter
                    </button>
                </form>

                @forelse($categories as $category)
                    <div class="flex justify-between items-center border-b py-3 last:border-0">
                        <span class="text-gray-700">{{ $category->name }}</span>
                        <form method="POST"
                            action="{{ route('categories.destroy', [$colocation, $category]) }}"
                            onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:underline">Supprimer</button>
                        </form>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm text-center py-4">Aucune catégorie pour l'instant.</p>
                @endforelse
            </div>

            <a href="{{ route('colocations.show', $colocation) }}"
                class="block text-center text-sm text-gray-400 hover:text-orange-500">
                ← Retour à la colocation
            </a>
        </div>
    </div>
</x-app-layout>
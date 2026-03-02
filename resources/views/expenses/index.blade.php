<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-xl">✅ {{ session('success') }}</div>
            @endif

            <!-- Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">💸 Dépenses — {{ $colocation->name }}</h2>
                <a href="{{ route('expenses.create', $colocation) }}"
                    class="px-4 py-2 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition text-sm font-medium">
                    + Ajouter
                </a>
            </div>

            <!-- Filtre -->
            <div class="bg-white rounded-2xl shadow p-4">
                <form method="GET" action="{{ route('expenses.index', $colocation) }}" class="flex gap-3 items-end">
                    <div>
                        <x-input-label for="month" value="Filtrer par mois" />
                        <x-text-input id="month" name="month" type="month" class="mt-1 rounded-xl" :value="$request->month" />
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition text-sm">
                        Filtrer
                    </button>
                    @if($request->filled('month'))
                        <a href="{{ route('expenses.index', $colocation) }}"
                            class="text-sm text-gray-400 hover:text-orange-500 self-center">
                            Réinitialiser
                        </a>
                    @endif
                </form>
            </div>

            <!-- Liste -->
            <div class="bg-white rounded-2xl shadow divide-y">
                @forelse($expenses as $expense)
                    <div class="flex justify-between items-center p-5">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $expense->title }}</p>
                            <p class="text-xs text-gray-400 mt-1">
                                📅 {{ $expense->date->format('d/m/Y') }} •
                                👤 {{ $expense->payer->name }}
                                @if($expense->category)
                                    • 🏷️ {{ $expense->category->name }}
                                @endif
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="font-bold text-orange-500 text-lg">
                                {{ number_format($expense->amount, 2) }} €
                            </span>
                            <form method="POST"
                                action="{{ route('expenses.destroy', [$colocation, $expense]) }}"
                                onsubmit="return confirm('Supprimer cette dépense ?')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-red-400 hover:underline">Supprimer</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-gray-400">
                        <p class="text-3xl mb-2">📭</p>
                        <p>Aucune dépense pour cette période.</p>
                    </div>
                @endforelse
            </div>

            <a href="{{ route('colocations.show', $colocation) }}"
                class="block text-center text-sm text-gray-400 hover:text-orange-500">
                ← Retour à la colocation
            </a>

        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Carte de bienvenue -->
            <div class="bg-white rounded-2xl shadow p-8 mb-6 border-l-4 border-orange-500">
                <h1 class="text-2xl font-bold text-gray-800">
                    Bonjour, {{ auth()->user()->name }} 👋
                </h1>
                <p class="text-gray-500 mt-1 text-sm">
                    Réputation :
                    <span class="font-semibold text-orange-500">{{ auth()->user()->reputation }} pts</span>
                </p>
            </div>

            @if(auth()->user()->activeMembership)
                <!-- Carte colocation active -->
                <div class="bg-white rounded-2xl shadow p-8 border-l-4 border-green-400">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Colocation active</p>
                            <h2 class="text-xl font-bold text-gray-800">
                                {{ auth()->user()->activeMembership->colocation->name }}
                            </h2>
                            <span class="inline-block mt-2 text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded-full capitalize">
                                {{ auth()->user()->activeMembership->role }}
                            </span>
                        </div>
                        <a href="{{ route('colocations.show', auth()->user()->activeMembership->colocation) }}"
                            class="px-5 py-2 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition font-medium">
                            Voir →
                        </a>
                    </div>
                </div>
            @else
                <!-- Pas de colocation -->
                <div class="bg-white rounded-2xl shadow p-8 text-center border-2 border-dashed border-orange-300">
                    <p class="text-4xl mb-3">🏠</p>
                    <p class="text-gray-600 font-medium mb-4">Vous n'avez pas encore de colocation active.</p>
                    <a href="{{ route('colocations.create') }}"
                        class="inline-block px-6 py-3 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition font-medium">
                        + Créer une colocation
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
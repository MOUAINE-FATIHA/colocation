<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tableau de bord
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded shadow">
                <p>Bonjour <strong>{{ auth()->user()->name }}</strong></p>

                @if(auth()->user()->activeMembership)
                    <p class="mt-2 text-green-600">
                        Vous êtes membre de la colocation :
                        <strong>{{ auth()->user()->activeMembership->colocation->name }}</strong>
                    </p>
                    <a href="{{ route('colocations.show', auth()->user()->activeMembership->colocation) }}"
                        class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Voir ma colocation
                    </a>
                @else
                    <p class="mt-2 text-gray-500">
                        Vous n'avez pas encore de colocation active.
                    </p>
                    <a href="{{ route('colocations.create') }}"
                        class="inline-block mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Créer une colocation
                    </a>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
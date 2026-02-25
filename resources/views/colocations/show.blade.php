<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $colocation->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Infos colocation --}}
            <div class="bg-white p-6 rounded shadow">
                <p class="text-gray-500 text-sm">Statut :
                    <span class="font-semibold {{ $colocation->status === 'active' ? 'text-green-600' : 'text-red-500' }}">
                        {{ $colocation->status }}
                    </span>
                </p>
            </div>

            {{-- Liste des membres --}}
            <div class="bg-white p-6 rounded shadow">
                <h3 class="font-semibold text-lg mb-4">Membres</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="pb-2">Nom</th>
                            <th class="pb-2">Rôle</th>
                            <th class="pb-2">Réputation</th>
                            @if($membership->role === 'owner')
                                <th class="pb-2">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $m)
                        <tr class="border-b">
                            <td class="py-2">{{ $m->user->name }}</td>
                            <td class="py-2 capitalize">{{ $m->role }}</td>
                            <td class="py-2">{{ $m->user->reputation }}</td>
                            @if($membership->role === 'owner')
                                <td class="py-2">
                                    @if($m->role !== 'owner')
                                        <form method="POST"
                                            action="{{ route('colocations.removeMember', [$colocation, $m->user]) }}"
                                            onsubmit="return confirm('Retirer ce membre ?')">
                                            @csrf @method('DELETE')
                                            <button class="text-red-500 text-xs hover:underline">Retirer</button>
                                        </form>
                                    @endif
                                </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Actions --}}
            <div class="bg-white p-6 rounded shadow flex gap-4">
                @if($membership->role === 'owner')
                    <form method="POST" action="{{ route('colocations.cancel', $colocation) }}"
                        onsubmit="return confirm('Annuler la colocation ? Cette action est irréversible.')">
                        @csrf
                        <x-danger-button>Annuler la colocation</x-danger-button>
                    </form>
                @else
                    <form method="POST" action="{{ route('colocations.leave', $colocation) }}"
                        onsubmit="return confirm('Quitter la colocation ?')">
                        @csrf
                        <x-danger-button>Quitter la colocation</x-danger-button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
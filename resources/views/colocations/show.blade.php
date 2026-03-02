<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl">✅ {{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl">{{ $errors->first() }}</div>
            @endif

            <!-- Header -->
            <div class="bg-white rounded-2xl shadow p-6 flex justify-between items-center border-l-4 border-orange-500">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">🏠 {{ $colocation->name }}</h1>
                    <span class="inline-block mt-1 text-xs px-2 py-1 rounded-full font-medium
                        {{ $colocation->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                        {{ $colocation->status === 'active' ? '● Actif' : '● Annulé' }}
                    </span>
                </div>
                <span class="text-xs bg-orange-100 text-orange-700 px-3 py-1 rounded-full capitalize font-medium">
                    {{ $membership->role }}
                </span>
            </div>

            <!-- Membres -->
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="font-bold text-gray-700 text-lg mb-4">👥 Membres</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-400 border-b text-xs uppercase tracking-wide">
                            <th class="pb-3">Nom</th>
                            <th class="pb-3">Rôle</th>
                            <th class="pb-3">Réputation</th>
                            @if($membership->role === 'owner')<th class="pb-3">Action</th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $m)
                        <tr class="border-b last:border-0">
                            <td class="py-3 font-medium text-gray-800">{{ $m->user->name }}</td>
                            <td class="py-3">
                                <span class="text-xs px-2 py-1 rounded-full
                                    {{ $m->role === 'owner' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $m->role }}
                                </span>
                            </td>
                            <td class="py-3 text-gray-600">{{ $m->user->reputation }} pts</td>
                            @if($membership->role === 'owner')
                            <td class="py-3">
                                @if($m->role !== 'owner')
                                    <form method="POST"
                                        action="{{ route('colocations.removeMember', [$colocation, $m->user]) }}"
                                        onsubmit="return confirm('Retirer {{ $m->user->name }} ?')">
                                        @csrf @method('DELETE')
                                        <button class="text-xs text-red-500 hover:underline">Retirer</button>
                                    </form>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="font-bold text-gray-700 text-lg mb-4">⚡ Actions</h3>
                <div class="flex flex-wrap gap-3">

                    <a href="{{ route('expenses.index', $colocation) }}"
                        class="px-4 py-2 bg-gray-700 text-white rounded-xl hover:bg-gray-800 transition text-sm">
                        💸 Dépenses
                    </a>

                    <a href="{{ route('balances.index', $colocation) }}"
                        class="px-4 py-2 bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 transition text-sm">
                        ⚖️ Balances
                    </a>

                    @if($membership->role === 'owner')
                        <a href="{{ route('invitations.create', $colocation) }}"
                            class="px-4 py-2 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition text-sm">
                            ✉️ Inviter
                        </a>
                        <a href="{{ route('categories.index', $colocation) }}"
                            class="px-4 py-2 bg-yellow-400 text-white rounded-xl hover:bg-yellow-500 transition text-sm">
                            Catégories
                        </a>
                        <form method="POST" action="{{ route('colocations.cancel', $colocation) }}"
                            onsubmit="return confirm('Annuler la colocation ?')">
                            @csrf
                            <button class="px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition text-sm">
                                Annuler
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('colocations.leave', $colocation) }}"
                            onsubmit="return confirm('Quitter la colocation ?')">
                            @csrf
                            <button class="px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition text-sm">
                                Quitter
                            </button>
                        </form>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
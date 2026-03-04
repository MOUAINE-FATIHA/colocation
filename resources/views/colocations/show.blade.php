<x-app-layout>
<div class="py-10 bg-gray-50 min-h-screen">
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl">{{ $errors->first() }}</div>
    @endif

    {{-- ===== HEADER COLOCATION ===== --}}
    <div class="bg-white rounded-2xl shadow p-6 flex justify-between items-center border-l-4 border-orange-500">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $colocation->name }}</h1>
            <span class="inline-block mt-1 text-xs px-2 py-1 rounded-full font-medium
                {{ $colocation->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                {{ $colocation->status === 'active' ? 'Actif' : 'Annulé' }}
            </span>
        </div>
        <span class="text-xs bg-orange-100 text-orange-700 px-3 py-1 rounded-full capitalize font-medium">
            {{ $membership->role }}
        </span>
    </div>

    {{-- ===== SECTION MEMBRES ===== --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-800">Membres</h2>
            @if($membership->role === 'owner')
                <a href="{{ route('invitations.create', $colocation) }}"
                    class="px-4 py-2 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition text-sm">
                    Inviter
                </a>
            @endif
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-400 text-xs uppercase tracking-wide border-b">
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
                                <button class="text-xs text-red-400 hover:underline">Retirer</button>
                            </form>
                        @endif
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Actions Owner / Member --}}
        <div class="mt-4 flex flex-wrap gap-3 border-t pt-4">
            @if($membership->role === 'owner')
                @if($colocation->status === 'active')
                    <form method="POST" action="{{ route('colocations.cancel', $colocation) }}"
                        onsubmit="return confirm('Annuler la colocation ?')">
                        @csrf
                        <button class="px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition text-sm">
                            Annuler la colocation
                        </button>
                    </form>
                @endif
                @if($membership->role === 'owner')
                    <a href="{{ route('categories.index', $colocation) }}"
                        class="px-4 py-2 bg-yellow-400 text-white rounded-xl hover:bg-yellow-500 transition text-sm">
                        Catégories
                    </a>
                @endif
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

    {{-- ===== SECTION DÉPENSES ===== --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold text-gray-800">Dépenses</h2>
            <a href="{{ route('expenses.create', $colocation) }}"
                class="px-4 py-2 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition text-sm">
                Ajouter
            </a>
        </div>

        {{-- Filtre par mois --}}
        <form method="GET" action="{{ route('colocations.show', $colocation) }}"
            class="flex gap-3 items-end mb-4">
            <div>
                <x-input-label for="month" value="Filtrer par mois" />
                <x-text-input id="month" name="month" type="month"
                    class="mt-1 rounded-xl" :value="$request->month" />
            </div>
            <button type="submit"
                class="px-4 py-2 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition text-sm">
                Filtrer
            </button>
            @if($request->filled('month'))
                <a href="{{ route('colocations.show', $colocation) }}"
                    class="text-sm text-gray-400 hover:text-orange-500 self-center">
                    Réinitialiser
                </a>
            @endif
        </form>

        {{-- Liste dépenses --}}
        <div class="divide-y">
            @forelse($expenses as $expense)
                <div class="flex justify-between items-center py-3">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ $expense->title }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $expense->date->format('d/m/Y') }} •
                            {{ $expense->payer->name }}
                            @if($expense->category) • {{ $expense->category->name }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-orange-500">{{ number_format($expense->amount, 2) }} $</span>
                        <form method="POST"
                            action="{{ route('expenses.destroy', [$colocation, $expense]) }}"
                            onsubmit="return confirm('Supprimer ?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-400 hover:underline">Supprimer</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-6 text-center text-gray-400">
                    <p class="text-2xl mb-1"></p>
                    <p class="text-sm">Aucune dépense pour cette période.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ===== SECTION SOLDES ===== --}}
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Soldes</h2>

        @if(empty($balances))
            <p class="text-gray-400 text-sm">Aucune donnée disponible.</p>
        @else
            <table class="w-full text-sm mb-6">
                <thead>
                    <tr class="text-left text-gray-400 text-xs uppercase tracking-wide border-b">
                        <th class="pb-3">Membre</th>
                        <th class="pb-3">Total payé</th>
                        <th class="pb-3">Part due</th>
                        <th class="pb-3">Solde</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($balances as $data)
                    <tr class="border-b last:border-0">
                        <td class="py-3 font-medium text-gray-800">{{ $data['user']->name }}</td>
                        <td class="py-3 text-gray-600">{{ number_format($data['paid'], 2) }} $</td>
                        <td class="py-3 text-gray-600">{{ number_format($data['share'], 2) }} $</td>
                        <td class="py-3 font-bold
                            {{ $data['balance'] > 0 ? 'text-green-500' : ($data['balance'] < 0 ? 'text-red-500' : 'text-gray-400') }}">
                            {{ $data['balance'] > 0 ? '+' : '' }}{{ number_format($data['balance'], 2) }} $
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Qui doit à qui --}}
            <h3 class="font-semibold text-gray-700 mb-3">Qui doit à qui ?</h3>
            @if(empty($settlements))
                <p class="text-center text-green-500 font-medium py-3">Tout le monde est quitte !</p>
            @else
                <ul class="space-y-2">
                    @foreach($settlements as $s)
                    <li class="flex justify-between items-center bg-gray-50 rounded-xl p-3">
                        <span class="text-sm">
                            <strong class="text-red-500">{{ $s['from']->name }}</strong>
                            doit <strong>{{ number_format($s['amount'], 2) }} $</strong>
                            à <strong class="text-green-600">{{ $s['to']->name }}</strong>
                        </span>
                        @if($s['from']->id === auth()->id())
                            <form method="POST" action="{{ route('payments.store', $colocation) }}">
                                @csrf
                                <input type="hidden" name="to_user_id" value="{{ $s['to']->id }}">
                                <input type="hidden" name="amount" value="{{ $s['amount'] }}">
                                <button type="submit"
                                    class="px-3 py-1 bg-orange-500 text-white text-xs rounded-xl hover:bg-orange-600"
                                    onclick="return confirm('Confirmer le paiement ?')">
                                    Marquer payé
                                </button>
                            </form>
                        @endif
                    </li>
                    @endforeach
                </ul>
            @endif
        @endif
    </div>

    {{-- ===== SECTION HISTORIQUE PAIEMENTS ===== --}}
    @if($paymentHistory->count() > 0)
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Historique des paiements</h2>
        <div class="divide-y">
            @foreach($paymentHistory as $payment)
                <div class="flex justify-between items-center py-3">
                    <div class="text-sm text-gray-700">
                        <strong class="text-red-500">{{ $payment->fromUser->name }}</strong>
                        a payé
                        <strong class="text-green-600">{{ $payment->toUser->name }}</strong>
                        <p class="text-xs text-gray-400 mt-1">{{ $payment->paid_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <span class="font-bold text-orange-500">{{ number_format($payment->amount, 2) }} $</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
</div>
</x-app-layout>
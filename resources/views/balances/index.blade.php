<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-xl">{{ session('success') }}</div>
            @endif

            <h2 class="text-2xl font-bold text-gray-800">Balances — {{ $colocation->name }}</h2>

            <!-- Soldes -->
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="font-bold text-gray-700 mb-4">Soldes individuels</h3>
                @if(empty($balances))
                    <p class="text-gray-400 text-sm">Aucune donnée disponible.</p>
                @else
                    <table class="w-full text-sm">
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
                @endif
            </div>

            <!-- Qui doit à qui -->
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="font-bold text-gray-700 mb-4">Qui doit à qui ?</h3>
                @if(empty($settlements))
                    <p class="text-center text-green-500 font-medium py-4">Tout le monde est quitte !</p>
                @else
                    <ul class="space-y-3">
                        @foreach($settlements as $s)
                        <li class="flex justify-between items-center bg-gray-50 rounded-xl p-4">
                            <span class="text-sm">
                                <strong class="text-red-500">{{ $s['from']->name }}</strong>
                                doit
                                <strong class="text-gray-800">{{ number_format($s['amount'], 2) }} $</strong>
                                à
                                <strong class="text-green-600">{{ $s['to']->name }}</strong>
                            </span>
                            @if($s['from']->id === auth()->id())
                                <form method="POST" action="{{ route('payments.store', $colocation) }}">
                                    @csrf
                                    <input type="hidden" name="to_user_id" value="{{ $s['to']->id }}">
                                    <input type="hidden" name="amount" value="{{ $s['amount'] }}">
                                    <button type="submit"
                                        class="px-3 py-1 bg-orange-500 text-white text-xs rounded-xl hover:bg-orange-600 transition"
                                        onclick="return confirm('Confirmer le paiement ?')">
                                        Marquer payé
                                    </button>
                                </form>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Liens -->
            <div class="flex justify-between items-center">
                <a href="{{ route('colocations.show', $colocation) }}"
                    class="text-sm text-gray-400 hover:text-orange-500">
                    Retour
                </a>
                <a href="{{ route('payments.index', $colocation) }}"
                    class="px-4 py-2 bg-gray-700 text-white rounded-xl hover:bg-gray-800 transition text-sm">
                    Historique paiements
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
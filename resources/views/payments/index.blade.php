<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <h2 class="text-2xl font-bold text-gray-800">📋 Historique des paiements — {{ $colocation->name }}</h2>

            <div class="bg-white rounded-2xl shadow divide-y">
                @forelse($payments as $payment)
                    <div class="flex justify-between items-center p-5">
                        <div>
                            <p class="text-sm text-gray-700">
                                <strong class="text-red-500">{{ $payment->fromUser->name }}</strong>
                                a payé
                                <strong class="text-green-600">{{ $payment->toUser->name }}</strong>
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                📅 {{ $payment->paid_at->format('d/m/Y à H:i') }}
                            </p>
                        </div>
                        <span class="font-bold text-orange-500 text-lg">
                            {{ number_format($payment->amount, 2) }} €
                        </span>
                    </div>
                @empty
                    <div class="p-10 text-center text-gray-400">
                        <p class="text-3xl mb-2">📭</p>
                        <p>Aucun paiement enregistré.</p>
                    </div>
                @endforelse
            </div>

            <a href="{{ route('balances.index', $colocation) }}"
                class="block text-center text-sm text-gray-400 hover:text-orange-500">
                ← Retour aux balances
            </a>

        </div>
    </div>
</x-app-layout>
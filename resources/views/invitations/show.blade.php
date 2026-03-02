<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow p-10 text-center">
                <p class="text-5xl mb-4">🏠</p>
                <h2 class="text-xl font-bold text-gray-800 mb-2">Invitation reçue !</h2>
                <p class="text-gray-500 mb-1">Vous êtes invité à rejoindre :</p>
                <p class="text-2xl font-bold text-orange-500 mb-8">{{ $invitation->colocation->name }}</p>

                <div class="flex justify-center gap-4">
                    <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}">
                        @csrf
                        <button type="submit"
                            class="px-6 py-3 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition font-medium">
                            ✅ Accepter
                        </button>
                    </form>
                    <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}">
                        @csrf
                        <button type="submit"
                            class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 transition font-medium">
                            ❌ Refuser
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
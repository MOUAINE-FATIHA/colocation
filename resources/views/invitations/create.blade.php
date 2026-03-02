<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">✉️ Inviter un membre</h2>
                <p class="text-gray-400 text-sm mb-6">{{ $colocation->name }}</p>

                @if(session('success'))
                    <div class="mb-4 bg-green-50 text-green-700 text-sm p-3 rounded-xl">✅ {{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 bg-red-50 text-red-600 text-sm p-3 rounded-xl">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('invitations.store', $colocation) }}">
                    @csrf
                    <div class="mb-6">
                        <x-input-label for="email" value="Adresse email" />
                        <x-text-input id="email" name="email" type="email"
                            class="mt-1 block w-full rounded-xl"
                            placeholder="exemple@email.com" required />
                    </div>
                    <button type="submit"
                        class="w-full py-3 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition font-medium">
                        Envoyer l'invitation
                    </button>
                </form>

                <a href="{{ route('colocations.show', $colocation) }}"
                    class="block text-center mt-4 text-sm text-gray-400 hover:text-orange-500">
                    ← Retour
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
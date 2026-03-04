<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Créer une colocation</h2>

                @if($errors->any())
                    <div class="mb-4 bg-red-50 text-red-600 text-sm p-3 rounded-xl">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('colocations.store') }}">
                    @csrf
                    <div class="mb-6">
                        <x-input-label for="name" value="Nom de la colocation" />
                        <x-text-input id="name" name="name" type="text"
                            class="mt-1 block w-full rounded-xl border-gray-300"
                            :value="old('name')" placeholder="Ex: Appart des amis" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <button type="submit"
                        class="w-full py-3 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition font-medium">
                        Créer la colocation
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
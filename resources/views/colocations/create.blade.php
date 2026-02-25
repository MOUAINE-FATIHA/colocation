<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Créer une colocation
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">

                @if($errors->any())
                    <div class="mb-4 text-red-600 text-sm">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('colocations.store') }}">
                    @csrf
                    <div class="mb-4">
                        <x-input-label for="name" value="Nom de la colocation" />
                        <x-text-input id="name" name="name" type="text"
                            class="mt-1 block w-full" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <x-primary-button>Créer</x-primary-button>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
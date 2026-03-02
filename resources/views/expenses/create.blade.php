<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">💸 Ajouter une dépense</h2>

                @if($errors->any())
                    <div class="mb-4 bg-red-50 text-red-600 text-sm p-3 rounded-xl">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('expenses.store', $colocation) }}" class="space-y-5">
                    @csrf

                    <div>
                        <x-input-label for="title" value="Titre" />
                        <x-text-input id="title" name="title" type="text"
                            class="mt-1 block w-full rounded-xl" :value="old('title')"
                            placeholder="Ex: Courses Carrefour" required />
                    </div>

                    <div>
                        <x-input-label for="amount" value="Montant (€)" />
                        <x-text-input id="amount" name="amount" type="number"
                            step="0.01" min="0.01" class="mt-1 block w-full rounded-xl"
                            :value="old('amount')" placeholder="0.00" required />
                    </div>

                    <div>
                        <x-input-label for="date" value="Date" />
                        <x-text-input id="date" name="date" type="date"
                            class="mt-1 block w-full rounded-xl"
                            :value="old('date', date('Y-m-d'))" required />
                    </div>

                    <div>
                        <x-input-label for="paid_by" value="Payé par" />
                        <select name="paid_by" id="paid_by"
                            class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm">
                            @foreach($members as $m)
                                <option value="{{ $m->user->id }}"
                                    {{ old('paid_by', auth()->id()) == $m->user->id ? 'selected' : '' }}>
                                    {{ $m->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="category_id" value="Catégorie (optionnel)" />
                        <select name="category_id" id="category_id"
                            class="mt-1 block w-full border-gray-300 rounded-xl shadow-sm">
                            <option value="">-- Aucune --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition font-medium">
                        Ajouter la dépense
                    </button>
                </form>

                <a href="{{ route('expenses.index', $colocation) }}"
                    class="block text-center mt-4 text-sm text-gray-400 hover:text-orange-500">
                    ← Retour
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
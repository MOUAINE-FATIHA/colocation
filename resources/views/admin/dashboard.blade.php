<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Admin Global
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded shadow">
                    <p class="text-gray-500">Utilisateurs</p>
                    <p class="text-3xl font-bold">{{ $stats['total_users'] }}</p>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    <p class="text-gray-500">Colocations</p>
                    <p class="text-3xl font-bold">{{ $stats['total_colocations'] }}</p>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    <p class="text-gray-500">Dépenses</p>
                    <p class="text-3xl font-bold">{{ $stats['total_expenses'] }}</p>
                </div>
                <div class="bg-white p-6 rounded shadow">
                    <p class="text-gray-500">Utilisateurs bannis</p>
                    <p class="text-3xl font-bold text-red-500">{{ $stats['banned_users'] }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
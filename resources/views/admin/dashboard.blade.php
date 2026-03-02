<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 text-green-700 p-4 rounded-xl">✅ {{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="bg-red-50 text-red-600 p-4 rounded-xl">{{ $errors->first() }}</div>
            @endif

            <h2 class="text-2xl font-bold text-gray-800">⚙️ Dashboard Admin</h2>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow p-6 text-center border-t-4 border-orange-500">
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
                    <p class="text-gray-400 text-sm mt-1">Utilisateurs</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-6 text-center border-t-4 border-blue-400">
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_colocations'] }}</p>
                    <p class="text-gray-400 text-sm mt-1">Colocations</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-6 text-center border-t-4 border-green-400">
                    <p class="text-3xl font-bold text-gray-800">{{ $stats['total_expenses'] }}</p>
                    <p class="text-gray-400 text-sm mt-1">Dépenses</p>
                </div>
                <div class="bg-white rounded-2xl shadow p-6 text-center border-t-4 border-red-400">
                    <p class="text-3xl font-bold text-red-500">{{ $stats['banned_users'] }}</p>
                    <p class="text-gray-400 text-sm mt-1">Bannis</p>
                </div>
            </div>

            <!-- Utilisateurs -->
            <div class="bg-white rounded-2xl shadow p-6">
                <h3 class="font-bold text-gray-700 text-lg mb-4">👥 Utilisateurs</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-400 text-xs uppercase tracking-wide border-b">
                            <th class="pb-3">Nom</th>
                            <th class="pb-3">Email</th>
                            <th class="pb-3">Réputation</th>
                            <th class="pb-3">Rôle</th>
                            <th class="pb-3">Statut</th>
                            <th class="pb-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="border-b last:border-0">
                            <td class="py-3 font-medium text-gray-800">{{ $user->name }}</td>
                            <td class="py-3 text-gray-400 text-xs">{{ $user->email }}</td>
                            <td class="py-3 text-gray-600">{{ $user->reputation }} pts</td>
                            <td class="py-3">
                                @if($user->is_admin)
                                    <span class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded-full">Admin</span>
                                @else
                                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded-full">User</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($user->is_banned)
                                    <span class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded-full">Banni</span>
                                @else
                                    <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full">Actif</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if(!$user->is_admin)
                                    @if($user->is_banned)
                                        <form method="POST" action="{{ route('admin.users.unban', $user) }}"
                                            onsubmit="return confirm('Débannir {{ $user->name }} ?')">
                                            @csrf
                                            <button class="text-xs text-green-500 hover:underline">Débannir</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.ban', $user) }}"
                                            onsubmit="return confirm('Bannir {{ $user->name }} ?')">
                                            @csrf
                                            <button class="text-xs text-red-400 hover:underline">Bannir</button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-gray-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
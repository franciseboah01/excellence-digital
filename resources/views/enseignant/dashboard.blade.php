<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            👨‍🏫 Espace Enseignant
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-lg font-bold text-blue-800">
                    Bienvenue, {{ auth()->user()->nom_complet }} !
                </p>
                <p class="text-gray-600 mt-2">
                    Vous êtes connecté en tant qu'Enseignant.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
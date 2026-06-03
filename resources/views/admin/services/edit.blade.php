@extends('layouts.admin')
@section('title', 'Modifier Service')
@section('page_title', 'Modifier le Service')

@section('content')
<div class="mt-6 max-w-2xl">
    <a href="{{ route('admin.services.index') }}"
        class="text-blue-600 hover:underline text-sm">← Retour</a>

    <div class="bg-white rounded-xl shadow p-8 mt-4">
        <form method="POST" action="{{ route('admin.services.update', $service) }}">
            @csrf @method('PUT')
            @include('admin.services.partials.form', ['service' => $service])

            {{-- Toggle Actif --}}
            <div class="mt-4 flex items-center space-x-3">
                <input type="checkbox" name="actif" id="actif" value="1"
                    {{ $service->actif ? 'checked' : '' }}
                    class="w-4 h-4 text-blue-600">
                <label for="actif" class="text-sm font-semibold text-gray-700">
                    Service actif (visible sur le site)
                </label>
            </div>

            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition mt-6">
                💾 Enregistrer les modifications
            </button>
        </form>
    </div>
</div>
@endsection
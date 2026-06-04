@extends('layouts.admin')
@section('title', 'Modifier Formation')
@section('page_title', 'Modifier la Formation')

@section('content')
<div class="mt-6 max-w-2xl">
    <a href="{{ route('admin.formations.show', $formation) }}"
        class="text-blue-600 hover:underline text-sm">← Retour</a>

    <div class="bg-white rounded-xl shadow p-8 mt-4">
        <form method="POST" action="{{ route('admin.formations.update', $formation) }}"
            enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('admin.formations.partials.form', ['formation' => $formation])
            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition mt-6">
                💾 Enregistrer les modifications
            </button>
        </form>
    </div>
</div>
@endsection
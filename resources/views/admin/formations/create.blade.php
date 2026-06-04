@extends('layouts.admin')
@section('title', 'Nouvelle Formation')
@section('page_title', 'Créer une Formation')

@section('content')
<div class="mt-6 max-w-2xl">
    <a href="{{ route('admin.formations.index') }}"
        class="text-blue-600 hover:underline text-sm">← Retour</a>

    <div class="bg-white rounded-xl shadow p-8 mt-4">
        <form method="POST" action="{{ route('admin.formations.store') }}"
            enctype="multipart/form-data">
            @csrf
            @include('admin.formations.partials.form')
            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition mt-6">
                ➕ Créer la formation
            </button>
        </form>
    </div>
</div>
@endsection
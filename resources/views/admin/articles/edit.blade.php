@extends('layouts.admin')
@section('title', 'Modifier Article')
@section('page_title', 'Modifier l\'Article')

@section('content')
<div class="mt-6 max-w-3xl">
    <a href="{{ route('admin.articles.index') }}"
        class="text-blue-600 hover:underline text-sm">← Retour</a>

    <div class="bg-white rounded-xl shadow p-8 mt-4">
        <form method="POST" action="{{ route('admin.articles.update', $article) }}"
            enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('admin.articles.partials.form', ['article' => $article])
            <button type="submit"
                class="w-full bg-blue-800 text-white py-3 rounded-xl font-bold hover:bg-blue-900 transition mt-6">
                💾 Enregistrer
            </button>
        </form>
    </div>
</div>
@endsection
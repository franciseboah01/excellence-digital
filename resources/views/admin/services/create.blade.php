@extends('layouts.admin')
@section('title', 'Nouveau Service')
@section('page_title', '➕ Créer un Service')

@section('content')
<div class="mt-6 max-w-2xl">
    <a href="{{ route('admin.services.index') }}"
        class="inline-flex items-center space-x-1 text-sm font-medium hover:underline"
        style="color: var(--edc-primary-light);">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span>Retour</span>
    </a>

    <div class="edc-card p-6 sm:p-8 mt-4">
        <form method="POST" action="{{ route('admin.services.store') }}" class="space-y-5">
            @csrf
            @include('admin.services.partials.form')
            <button type="submit" class="btn-primary w-full">➕ Créer le service</button>
        </form>
    </div>
</div>
@endsection
@extends('layouts.minimal')

@section('head')
<style>
    body {
        display: flex;
        align-items: center;
        padding-top: 40px;
        padding-bottom: 40px;
    }

    .form-signin {
        max-width: 330px;
        padding: 15px;
    }
</style>
@endsection

@section('content')
    <main class="form-signin w-100 m-auto">
        <h1 class="h3 mb-3 text-primary">
            <x-full-logo />
        </h1>
        @vue(Login)
        <p class="mt-3 text-muted">
            <span class="mr-1">By SSL with</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="inline w-auto align-top" style="height: 1.25rem;" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
            </svg>
        </p>
    </main>
@endsection

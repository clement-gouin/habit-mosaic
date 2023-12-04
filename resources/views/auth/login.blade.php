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
            By <a href="https://github.com/clement-gouin/">@clement-gouin</a> with <i class="fa-solid fa-heart"></i>
        </p>
    </main>
@endsection

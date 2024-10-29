@extends('layouts.minimal')

@section('content')
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content flex-col lg:flex-row-reverse">
            <div class="text-center lg:text-left">
                <h1 class="text-5xl font-bold text-primary-500"><x-full-logo /></h1>
            </div>
            <div class="card bg-base-100 w-full max-w-sm shrink-0 shadow-2xl">
                @vue(Login)
            </div>
        </div>
    </div>
@endsection

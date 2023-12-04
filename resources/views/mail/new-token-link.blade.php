<x-mail::message>
@if ($newUser)
# Welcome, {{ $user }}
@else
# Welcome back, {{ $user }}
@endif

<x-mail::button :url="$url">
Open App
</x-mail::button>

(Available for an hour)

Enjoy your stay !<br>
{{ config('app.name') }}
</x-mail::message>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>
<h3 style="margin: 0">Informations:</h3>
<ul style="list-style: none; margin: .5em 0">
    <li style="line-height: 1.5em">Date : {{ $date }}</li>
    <li style="line-height: 1.5em">Exception : {{ $exception }}</li>
    <li style="line-height: 1.5em">File : {{ $file }} : {{ $line}}</li>
</ul>
<h3 style="margin: 0">Message:</h3>
<pre style="margin: 1em 0 1em 40px">{{ $exception_message }}</pre>
<h3 style="margin: 0">Inputs:</h3>
@if($inputs)
    {{ print_r($inputs, true) }}
@else
    <pre style="margin: 1em 0 1em 40px">(Nothing)</pre>
@endif
<h3 style="margin: 0">Stacktrace</h3>
<ul style="list-style: none; margin: .5em">
    <li style="margin-bottom: 0.5em;">
        {{ $file }}:<b>{{ $line }}</b>
    </li>
    @foreach($trace as $row)
        <li style="margin-bottom: 0.5em;">

            @if(isset($row['file']))
                {{ $row['file'] }}:<b>{{ $row['line'] }}</b>
            @endif

            @if(isset($row['class']))
                <span style="color: orange">
                    {{ $row['class'].$row['type'] }}
                </span>
            @endif
            @if(array_key_exists('function', (array) $row))
                <span style="color: brown">
                    {{ $row['function'] }}
                </span>
            @endif
        </li>
    @endforeach
</ul>

</body>
</html>

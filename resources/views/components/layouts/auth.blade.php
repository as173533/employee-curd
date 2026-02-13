<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? 'Login' }}</title>
    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    @livewireStyles
</head>

<body style="font-family: Arial; padding:40px;">

    {{ $slot }}

    @livewireScripts
</body>
</html>

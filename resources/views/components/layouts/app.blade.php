<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? 'Dashboard' }}</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Icons --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    @livewireStyles

    <style>
        body { overflow-x: hidden; }
    </style>
</head>

<body class="bg-light">

    {{-- HEADER --}}
    @include('components.layouts.partials.header')

    <div class="d-flex">

        {{-- SIDEBAR --}}
        @include('components.layouts.partials.sidebar')

        {{-- PAGE CONTENT --}}
        <div class="flex-fill content-area p-4">
            {{ $slot }}
        </div>

    </div>

    {{-- FOOTER --}}
    @include('components.layouts.partials.footer')

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

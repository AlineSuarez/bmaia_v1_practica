<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'B-Maia')</title>
    @stack('styles')
</head>

<body>
    <main>
        @yield('content')
    </main>
</body>

</html>
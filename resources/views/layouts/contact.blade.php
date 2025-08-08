<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Contacto')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Fuentes y FA como en home -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&family=Playfair+Display:wght@700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/components/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/components/contact/contact.css') }}">
</head>

<body class="contact-page">
    @yield('content')
</body>

</html>
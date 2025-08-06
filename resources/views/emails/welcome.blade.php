<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .card {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #10b981;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>¡Hola {{ $user->name }}!</h1>
        <p>Gracias por registrarte en nuestra plataforma. Nos alegra tenerte con nosotros.</p>
        <p>A partir de ahora podrás acceder a todas las funciones del sistema. Si tienes dudas, contáctanos.</p>
        <p>— El equipo de soporte</p>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Celrem</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">
    <main class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="text-center">
            <h1 class="display-5 fw-bold mb-3">Sistema Celrem</h1>
            <p class="text-muted mb-4">Administración de clientes, servicios e inventario del taller.</p>

            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    Ir al panel principal
                </a>
            @else
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">
                        Crear cuenta
                    </a>
                </div>
            @endauth
        </div>
    </main>
</body>
</html>

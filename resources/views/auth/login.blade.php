<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Sistema de Taller</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

    <div class="container-fluid min-vh-100">
        <div class="row min-vh-100">

            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-dark text-white">
                <div class="px-5">
                    <h1 class="display-5 fw-bold mb-3">Taller Mecánico</h1>
                    <p class="lead mb-4">
                        Sistema de seguimiento de mantenimiento preventivo,
                        refacciones y lubricantes.
                    </p>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card bg-secondary border-0 text-white">
                                <div class="card-body">
                                    <h5 class="mb-1">Control de servicios</h5>
                                    <p class="mb-0 small">
                                        Administra clientes, vehículos y órdenes de trabajo.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card bg-secondary border-0 text-white">
                                <div class="card-body">
                                    <h5 class="mb-1">Inventario inteligente</h5>
                                    <p class="mb-0 small">
                                        Controla refacciones, aceites, lubricantes y consumibles.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card bg-secondary border-0 text-white">
                                <div class="card-body">
                                    <h5 class="mb-1">Alertas preventivas</h5>
                                    <p class="mb-0 small">
                                        Detecta mantenimientos próximos y productos con bajo stock.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                <div class="w-100" style="max-width: 430px;">

                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Iniciar sesión</h2>
                        <p class="text-muted">
                            Ingresa tus credenciales para acceder al sistema.
                        </p>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form action="{{ route('login.post') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}"
                                        placeholder="admin@taller.com"
                                        required
                                        autofocus
                                    >

                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input
                                        type="password"
                                        name="password"
                                        id="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="Ingresa tu contraseña"
                                        required
                                    >

                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="remember"
                                            id="remember"
                                        >
                                        <label class="form-check-label" for="remember">
                                            Recordarme
                                        </label>
                                    </div>

                                    <a href="#" class="text-decoration-none small">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    Entrar al sistema
                                </button>
                            </form>
                        </div>
                    </div>

                    <p class="text-center text-muted small mt-4">
                        Sistema de Mantenimiento Preventivo © 2026
                    </p>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta | Sistema Celrem</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-light">

    <div class="container-fluid min-vh-100">
        <div class="row min-vh-100">

            <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center bg-dark text-white">
                <div class="px-5">
                    <h1 class="display-5 fw-bold mb-3">Celrem Zitácuaro</h1>
                    <p class="lead mb-4">
                        Crea una cuenta para administrar clientes, servicios e inventario desde el sistema.
                    </p>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card bg-secondary border-0 text-white">
                                <div class="card-body">
                                    <h5 class="mb-1">Acceso protegido</h5>
                                    <p class="mb-0 small">
                                        Tus datos se guardan en MySQL y la contraseña se almacena cifrada.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card bg-secondary border-0 text-white">
                                <div class="card-body">
                                    <h5 class="mb-1">Inicio inmediato</h5>
                                    <p class="mb-0 small">
                                        Después del registro entrarás directo al panel principal.
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
                        <h2 class="fw-bold">Crear cuenta</h2>
                        <p class="text-muted">
                            Registra un usuario para poder iniciar sesión.
                        </p>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    {{ $errors->first() }}
                                </div>
                            @endif

                            <form action="{{ route('register.post') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre</label>
                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name') }}"
                                        placeholder="Nombre completo"
                                        required
                                        autofocus
                                    >

                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}"
                                        placeholder="usuario@taller.com"
                                        required
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
                                        placeholder="Mínimo 8 caracteres"
                                        required
                                    >

                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        id="password_confirmation"
                                        class="form-control"
                                        placeholder="Repite tu contraseña"
                                        required
                                    >
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    Crear cuenta
                                </button>
                            </form>

                            <div class="text-center mt-4">
                                <span class="text-muted small">¿Ya tienes una cuenta?</span>
                                <a href="{{ route('login') }}" class="text-decoration-none small fw-semibold">
                                    Iniciar sesión
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>

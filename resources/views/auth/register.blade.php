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
                    <p class="lead mb-4">Crea una cuenta para acceder al sistema según tu perfil.</p>
                </div>
            </div>

            <div class="col-lg-6 d-flex align-items-center justify-content-center">
                <div class="w-100 py-4" style="max-width: 460px;">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Crear cuenta</h2>
                        <p class="text-muted">Selecciona si entrarás como mecánico o cliente.</p>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4">
                            @if ($errors->any())
                                <div class="alert alert-danger">{{ $errors->first() }}</div>
                            @endif

                            <form action="{{ route('register.post') }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="rol" class="form-label">Tipo de cuenta</label>
                                    <select name="rol" id="rol" class="form-select @error('rol') is-invalid @enderror" required>
                                        <option value="">Selecciona una opción</option>
                                        <option value="mecanico" @selected(old('rol') === 'mecanico')>Mecánico</option>
                                        <option value="cliente" @selected(old('rol', 'cliente') === 'cliente')>Cliente</option>
                                    </select>
                                    @error('rol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nombre</label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required autofocus>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3 cliente-fields">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" id="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono') }}">
                                    @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3 cliente-fields">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <input type="text" name="direccion" id="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion') }}">
                                    @error('direccion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Mínimo 8 caracteres" required>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">Crear cuenta</button>
                            </form>

                            <div class="text-center mt-4">
                                <span class="text-muted small">¿Ya tienes una cuenta?</span>
                                <a href="{{ route('login') }}" class="text-decoration-none small fw-semibold">Iniciar sesión</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const rol = document.getElementById('rol');
            const clienteFields = document.querySelectorAll('.cliente-fields');
            const telefono = document.getElementById('telefono');

            const actualizarCampos = function () {
                const esCliente = rol.value === 'cliente';
                clienteFields.forEach((field) => field.classList.toggle('d-none', !esCliente));
                telefono.required = esCliente;
            };

            rol.addEventListener('change', actualizarCampos);
            actualizarCampos();
        });
    </script>
</body>
</html>

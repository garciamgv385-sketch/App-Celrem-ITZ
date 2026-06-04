<aside class="bg-dark text-white min-vh-100 p-3" style="width: 260px;">
    <h4 class="mb-4">Servicio Celrem</h4>

    <nav class="nav flex-column">
        <a class="nav-link text-white" href="{{ route('dashboard') }}">Panel principal</a>
        <a class="nav-link text-white" href="{{ route('clientes.index') }}">Clientes</a>
        <a class="nav-link text-white" href="{{ route('citas.index') }}">Citas</a>
        <a class="nav-link text-white" href="{{ route('vehiculos.index') }}">Vehículos</a>
        <a class="nav-link text-white" href="{{ route('inventario.index') }}">Inventario</a>
        <a class="nav-link text-white" href="{{ route('compras.index') }}">Compras</a>
        <a class="nav-link text-white" href="{{ route('ventas.index') }}">Ventas</a>
        <a class="nav-link text-white" href="{{ route('proveedores.index') }}">Proveedores</a>
        <a class="nav-link text-white" href="#">Servicios</a>
        {{--
 
        <a class="nav-link text-white" href="#">Ordenes</a>
       
        <a class="nav-link text-white" href="#">Reportes</a>
        --}}
    </nav>
</aside>

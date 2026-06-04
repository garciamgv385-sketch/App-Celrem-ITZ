@php
    $fieldId = $producto?->id ?? 'nuevo';
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="nombre{{ $fieldId }}">Nombre del producto</label>
        <input
            type="text"
            id="nombre{{ $fieldId }}"
            name="nombre"
            class="form-control @error('nombre') is-invalid @enderror"
            value="{{ old('nombre', $producto?->nombre) }}"
            required
        >
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="categoria{{ $fieldId }}">Categoría</label>
        <input
            type="text"
            id="categoria{{ $fieldId }}"
            name="categoria"
            class="form-control @error('categoria') is-invalid @enderror"
            value="{{ old('categoria', $producto?->categoria) }}"
            placeholder="Filtros, aceites, refacciones"
            required
        >
        @error('categoria')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="marca{{ $fieldId }}">Marca</label>
        <input
            type="text"
            id="marca{{ $fieldId }}"
            name="marca"
            class="form-control @error('marca') is-invalid @enderror"
            value="{{ old('marca', $producto?->marca) }}"
        >
        @error('marca')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="sku{{ $fieldId }}">SKU / Código</label>
        <input
            type="text"
            id="sku{{ $fieldId }}"
            name="sku"
            class="form-control @error('sku') is-invalid @enderror"
            value="{{ old('sku', $producto?->sku) }}"
            placeholder="ACE-5W30"
        >
        @error('sku')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="unidad{{ $fieldId }}">Unidad</label>
        <select id="unidad{{ $fieldId }}" name="unidad" class="form-select @error('unidad') is-invalid @enderror" required>
            @foreach (['pieza', 'litro', 'galon', 'juego', 'caja', 'metro'] as $unidad)
                <option value="{{ $unidad }}" @selected(old('unidad', $producto?->unidad ?? 'pieza') === $unidad)>
                    {{ ucfirst($unidad) }}
                </option>
            @endforeach
        </select>
        @error('unidad')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="existencia{{ $fieldId }}">Existencia</label>
        <input
            type="number"
            id="existencia{{ $fieldId }}"
            name="existencia"
            class="form-control @error('existencia') is-invalid @enderror"
            value="{{ old('existencia', $producto?->existencia ?? 0) }}"
            min="0"
            required
        >
        @error('existencia')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="stock_minimo{{ $fieldId }}">Stock mínimo</label>
        <input
            type="number"
            id="stock_minimo{{ $fieldId }}"
            name="stock_minimo"
            class="form-control @error('stock_minimo') is-invalid @enderror"
            value="{{ old('stock_minimo', $producto?->stock_minimo ?? 0) }}"
            min="0"
            required
        >
        @error('stock_minimo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="precio_compra{{ $fieldId }}">Precio compra</label>
        <input
            type="number"
            id="precio_compra{{ $fieldId }}"
            name="precio_compra"
            class="form-control @error('precio_compra') is-invalid @enderror"
            value="{{ old('precio_compra', $producto?->precio_compra ?? 0) }}"
            min="0"
            step="0.01"
            required
        >
        @error('precio_compra')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="precio_venta{{ $fieldId }}">Precio venta</label>
        <input
            type="number"
            id="precio_venta{{ $fieldId }}"
            name="precio_venta"
            class="form-control @error('precio_venta') is-invalid @enderror"
            value="{{ old('precio_venta', $producto?->precio_venta ?? 0) }}"
            min="0"
            step="0.01"
            required
        >
        @error('precio_venta')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="estado{{ $fieldId }}">Estado</label>
        <select id="estado{{ $fieldId }}" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
            <option value="activo" @selected(old('estado', $producto?->estado ?? 'activo') === 'activo')>Activo</option>
            <option value="inactivo" @selected(old('estado', $producto?->estado) === 'inactivo')>Inactivo</option>
        </select>
        @error('estado')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="ubicacion{{ $fieldId }}">Ubicación</label>
        <input
            type="text"
            id="ubicacion{{ $fieldId }}"
            name="ubicacion"
            class="form-control @error('ubicacion') is-invalid @enderror"
            value="{{ old('ubicacion', $producto?->ubicacion) }}"
            placeholder="Estante A1"
        >
        @error('ubicacion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="proveedor{{ $fieldId }}">Proveedor</label>
        <input
            type="text"
            id="proveedor{{ $fieldId }}"
            name="proveedor"
            class="form-control @error('proveedor') is-invalid @enderror"
            value="{{ old('proveedor', $producto?->proveedor) }}"
        >
        @error('proveedor')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label" for="observaciones{{ $fieldId }}">Observaciones</label>
        <textarea
            id="observaciones{{ $fieldId }}"
            name="observaciones"
            class="form-control @error('observaciones') is-invalid @enderror"
            rows="3"
        >{{ old('observaciones', $producto?->observaciones) }}</textarea>
        @error('observaciones')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

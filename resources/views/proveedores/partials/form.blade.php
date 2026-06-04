@php
    $fieldId = $proveedor?->id ?? 'nuevo';
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="nombreProveedor{{ $fieldId }}">Nombre</label>
        <input type="text" id="nombreProveedor{{ $fieldId }}" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $proveedor?->nombre) }}" required>
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="rfcProveedor{{ $fieldId }}">RFC</label>
        <input type="text" id="rfcProveedor{{ $fieldId }}" name="rfc" class="form-control @error('rfc') is-invalid @enderror" value="{{ old('rfc', $proveedor?->rfc) }}">
        @error('rfc')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label" for="telefonoProveedor{{ $fieldId }}">Teléfono</label>
        <input type="text" id="telefonoProveedor{{ $fieldId }}" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $proveedor?->telefono) }}">
        @error('telefono')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="correoProveedor{{ $fieldId }}">Correo</label>
        <input type="email" id="correoProveedor{{ $fieldId }}" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $proveedor?->correo) }}">
        @error('correo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="contactoProveedor{{ $fieldId }}">Contacto</label>
        <input type="text" id="contactoProveedor{{ $fieldId }}" name="contacto" class="form-control @error('contacto') is-invalid @enderror" value="{{ old('contacto', $proveedor?->contacto) }}">
        @error('contacto')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label" for="estadoProveedor{{ $fieldId }}">Estado</label>
        <select id="estadoProveedor{{ $fieldId }}" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
            <option value="activo" @selected(old('estado', $proveedor?->estado ?? 'activo') === 'activo')>Activo</option>
            <option value="inactivo" @selected(old('estado', $proveedor?->estado) === 'inactivo')>Inactivo</option>
        </select>
        @error('estado')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label" for="direccionProveedor{{ $fieldId }}">Dirección</label>
        <input type="text" id="direccionProveedor{{ $fieldId }}" name="direccion" class="form-control @error('direccion') is-invalid @enderror" value="{{ old('direccion', $proveedor?->direccion) }}">
        @error('direccion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label" for="observacionesProveedor{{ $fieldId }}">Observaciones</label>
        <textarea id="observacionesProveedor{{ $fieldId }}" name="observaciones" class="form-control @error('observaciones') is-invalid @enderror" rows="3">{{ old('observaciones', $proveedor?->observaciones) }}</textarea>
        @error('observaciones')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

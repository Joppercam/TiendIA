@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Crear Cupón</h4>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary float-right">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('admin.coupons.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Código</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-secondary" id="generate-code">Generar</button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">Código único que los clientes usarán para aplicar el cupón.</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Tipo de Descuento</label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Porcentaje (%)</option>
                                        <option value="fixed_amount" {{ old('type') === 'fixed_amount' ? 'selected' : '' }}>Monto Fijo ($)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="value">Valor</label>
                                    <input type="number" step="0.01" class="form-control" id="value" name="value" value="{{ old('value') }}" required>
                                    <small class="form-text text-muted">
                                        Porcentaje o monto fijo de descuento según el tipo seleccionado.
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_purchase">Compra Mínima (opcional)</label>
                                    <input type="number" step="0.01" class="form-control" id="min_purchase" name="min_purchase" value="{{ old('min_purchase') }}">
                                    <small class="form-text text-muted">
                                        Monto mínimo de compra para aplicar el cupón.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="usage_limit">Límite de Uso (opcional)</label>
                                    <input type="number" class="form-control" id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}">
                                    <small class="form-text text-muted">
                                        Número máximo de veces que se puede usar el cupón. Dejar en blanco para uso ilimitado.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="starts_at">Fecha de Inicio (opcional)</label>
                                    <input type="datetime-local" class="form-control" id="starts_at" name="starts_at" value="{{ old('starts_at') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expires_at">Fecha de Expiración (opcional)</label>
                                    <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Descripción (opcional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Activo</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Guardar Cupón</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('generate-code').addEventListener('click', function() {
        fetch('{{ route("admin.coupons.generate-code") }}')
            .then(response => response.json())
            .then(data => {
                document.getElementById('code').value = data.code;
            });
    });
</script>
@endpush
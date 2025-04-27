@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Editar Cupón</h4>
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
                    
                    <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Código</label>
                                    <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $coupon->code) }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">Tipo de Descuento</label>
                                    // resources/views/admin/coupons/edit.blade.php (continuación)
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Porcentaje (%)</option>
                                        <option value="fixed_amount" {{ old('type', $coupon->type) === 'fixed_amount' ? 'selected' : '' }}>Monto Fijo ($)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="value">Valor</label>
                                    <input type="number" step="0.01" class="form-control" id="value" name="value" value="{{ old('value', $coupon->value) }}" required>
                                    <small class="form-text text-muted">
                                        Porcentaje o monto fijo de descuento según el tipo seleccionado.
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_purchase">Compra Mínima (opcional)</label>
                                    <input type="number" step="0.01" class="form-control" id="min_purchase" name="min_purchase" value="{{ old('min_purchase', $coupon->min_purchase) }}">
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
                                    <input type="number" class="form-control" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}">
                                    <small class="form-text text-muted">
                                        Número máximo de veces que se puede usar el cupón. Dejar en blanco para uso ilimitado.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Usado hasta ahora</label>
                                    <input type="text" class="form-control" value="{{ $coupon->usage_count }} veces" disabled>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="starts_at">Fecha de Inicio (opcional)</label>
                                    <input type="datetime-local" class="form-control" id="starts_at" name="starts_at" value="{{ old('starts_at', $coupon->starts_at ? $coupon->starts_at->format('Y-m-d\TH:i') : '') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="expires_at">Fecha de Expiración (opcional)</label>
                                    <input type="datetime-local" class="form-control" id="expires_at" name="expires_at" value="{{ old('expires_at', $coupon->expires_at ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Descripción (opcional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $coupon->description) }}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Activo</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Actualizar Cupón</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
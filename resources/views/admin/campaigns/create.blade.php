@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Crear Campaña</h4>
                    <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary float-right">
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
                    
                    <form action="{{ route('admin.campaigns.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nombre de la Campaña</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="slug">Slug (opcional)</label>
                                    <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug') }}">
                                    <small class="form-text text-muted">
                                        URL amigable para la campaña. Si se deja en blanco, se generará automáticamente desde el nombre.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Descripción</label>
                            <textarea class="form-control" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="starts_at">Fecha de Inicio</label>
                                    <input type="datetime-local" class="form-control" id="starts_at" name="starts_at" value="{{ old('starts_at') }}" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ends_at">Fecha de Finalización</label>
                                    <input type="datetime-local" class="form-control" id="ends_at" name="ends_at" value="{{ old('ends_at') }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="banner_image">Imagen de Banner (opcional)</label>
                                    <input type="file" class="form-control-file" id="banner_image" name="banner_image">
                                    <small class="form-text text-muted">
                                        Imagen para mostrar en la página de la campaña. Tamaño recomendado: 1200x400px.
                                    </small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="banner_link">Enlace del Banner (opcional)</label>
                                    <input type="url" class="form-control" id="banner_link" name="banner_link" value="{{ old('banner_link') }}">
                                    <small class="form-text text-muted">
                                        URL a la que redirigirá el banner al hacer clic.
                                    </small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ old('is_active') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Activa</label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Guardar Campaña</button>
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
    // Script para generar slug automáticamente
    document.getElementById('name').addEventListener('keyup', function() {
        if (document.getElementById('slug').value === '') {
            const name = this.value;
            const slug = name
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
            document.getElementById('slug').value = slug;
        }
    });
</script>
@endpush
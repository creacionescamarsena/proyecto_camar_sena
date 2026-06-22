@extends('layouts.app_login')
@section('title', 'Registro')

@section('content')

<div class="container d-flex justify-content-center align-items-center login-container">
    <div class="col-12 col-md-6 col-lg-4">

        <div class="card login-card text-center">

            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo mx-auto">

            <h5 class="mb-4">Crea tu cuenta</h5>

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}">
                @csrf

                
                <div class="mb-3">
                    <select name="tipo_documento_id" class="form-control @error('tipo_documento_id') is-invalid @enderror" >
                        <option value="">Selecciona tipo de documento</option>
                        @foreach($tiposDocumento as $tipoDocumento)
                            <option value="{{ $tipoDocumento->id_tipo }}" {{ old('tipo_documento_id') == $tipoDocumento->id_tipo ? 'selected' : '' }}>{{ $tipoDocumento->tipo }}</option>
                        @endforeach
                    </select>
                    @error('tipo_documento_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>


                    <div class="mb-3">
                    <input type="text" name="id_usuario" class="form-control @error('id_usuario') is-invalid @enderror" placeholder="ID de usuario" value="{{ old('id_usuario') }}" >
                    @error('id_usuario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>


                <div class="mb-3">
                    <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror" placeholder="Nombres" value="{{ old('nombres') }}" >
                    @error('nombres')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <input type="text" name="apellidos" class="form-control @error('apellidos') is-invalid @enderror" placeholder="Apellidos" value="{{ old('apellidos') }}" >
                    @error('apellidos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" placeholder="Correo" value="{{ old('correo') }}" >
                    @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <input type="tel" name="telefono" class="form-control @error('telefono') is-invalid @enderror" placeholder="Teléfono (opcional)" value="{{ old('telefono') }}" minlength="8" maxlength="16">
                    @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña" >
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="Confirmar contraseña" >
                    @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-main w-100 mb-3">
                    Registrarme
                </button>

                <div class="mt-2 text-center">
                    <a href="{{ route('login') }}" class="d-block text-muted small" style="color: #adb5bd;">¿Ya tienes cuenta?</a>
                </div>
            </form>

        </div>

    </div>
</div>

@endsection
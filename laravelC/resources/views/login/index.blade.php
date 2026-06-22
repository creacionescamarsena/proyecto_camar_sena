@extends('layouts.app_login')
@section('title', 'index')

@section('content')

<div class="container d-flex justify-content-center align-items-center login-container">
    <div class="col-12 col-md-6 col-lg-4">
        

        <div class="card login-card text-center">

            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo mx-auto">
            

            <h5 class="mb-4">Inicia sesión en Creaciones Camar</h5>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="mb-3">
                    <input
                        type="email"
                        name="correo"
                        class="form-control @error('correo') is-invalid @enderror"
                        placeholder="Correo"
                        value="{{ old('correo') }}">
                    @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <input
                        type="password"
                        name="contraseña"
                        class="form-control @error('contraseña') is-invalid @enderror"
                        placeholder="Contraseña">
                    @error('contraseña')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-main w-100 mb-3">
                    Iniciar sesión
                </button>

                <div class="mt-2 text-center">
                    <a href="{{ route('register') }}" class="d-block text-muted small" style="color: #adb5bd;">Crear cuenta</a>
                    <a href="{{ route('password.request') }}" class="d-block text-muted small" style="color: #adb5bd;">¿Olvidaste tu contraseña?</a>
                </div>
            </form>

        </div>

    </div>
</div>

@endsection
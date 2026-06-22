@extends('layouts.app_login')
@section('title', 'Recuperar contraseña')

@section('content')

<div class="container d-flex justify-content-center align-items-center login-container">
    <div class="col-12 col-md-6 col-lg-4">

        <div class="card login-card text-center">

            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo mx-auto">

            <h5 class="mb-4">Recuperar contraseña</h5>

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

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" placeholder="Correo" value="{{ old('correo') }}">
                    @error('correo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <button type="submit" class="btn btn-main w-100 mb-3">
                    Enviar enlace de recuperación
                </button>

                <div class="mt-2 text-center">
                    <a href="{{ route('login') }}" class="d-block text-muted small" style="color: #adb5bd;">Volver al inicio de sesión</a>
                </div>
            </form>

        </div>

    </div>
</div>

@endsection
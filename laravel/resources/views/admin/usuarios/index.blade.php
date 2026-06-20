@extends('layouts.app')
@section('title', 'index')

@section('content')

<div class="container d-flex justify-content-center align-items-center login-container">
    <div class="col-12 col-md-6 col-lg-4">

        <div class="card login-card text-center">

            <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo mx-auto">

            <h5 class="mb-4">Inicia sesión en Creaciones Camar</h5>

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
                        class="form-control"
                        placeholder="Correo"
                        value="{{ old('email') }}">
                </div>

                <div class="mb-3">
                    <input
                        type="password"
                        name="contraseña"
                        class="form-control"
                        placeholder="Contraseña">
                </div>

                <div class="mb-3">
                    <select
                        name="rol"
                        class="form-control">
                        <option value="">Selecciona un rol</option>
                        <option value="Admin">Administrador</option>
                        <option value="Empleado">Empleado</option>
                        <option value="Cliente">Cliente</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-main w-100 mb-3">
                    Iniciar sesión
                </button>
            </form>

        </div>

    </div>
</div>

@endsection
@extends('layouts.auth')

@section('title', 'Login')  

@section('content')
<form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-floating mb-3">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                value="{{ old('email') }}" placeholder="E-mail" required autofocus>
            <label for="email">E‑mail</label>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating mb-3">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                name="password" placeholder="Senha" required>
            <label for="password">Senha</label>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Lembrar-me</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Entrar</button>

        <div class="mt-3 text-center">
            <a href="{{ route('register') }}">Registre-se</a>
        </div>

        @if (Route::has('password.request'))
            <div class="mt-2 text-center">
                <a href="{{ route('password.request') }}">Esqueceu sua senha?</a>
            </div>
        @endif
    </form>
@endsection

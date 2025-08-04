@extends('layouts.auth')

@section('title', 'Cadastro')  

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="form-floating mb-3">
            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                value="{{ old('name') }}" placeholder="Nome" required autofocus>
            <label for="name">Nome</label>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating mb-3">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                name="email" value="{{ old('email') }}" placeholder="E-mail" required>
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

        <div class="form-floating mb-3">
            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation"
                placeholder="Confirmar Senha" required>
            <label for="password_confirmation">Confirmar Senha</label>
        </div>

        <button type="submit" class="btn btn-primary w-100">Registrar</button>

        <div class="mt-3 text-center">
            <a href="{{ route('login') }}">Já tem uma conta? Entrar</a>
        </div>
    </form>
@endsection

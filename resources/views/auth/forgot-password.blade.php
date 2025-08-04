@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-floating mb-3">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                value="{{ old('email') }}" placeholder="E-mail" required autofocus>
            <label for="email">E‑mail</label>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Enviar link de recuperação
        </button>

        <div class="mt-3 text-center">
            <a href="{{ route('login') }}">Voltar para o login</a>
        </div>
    </form>
@endsection

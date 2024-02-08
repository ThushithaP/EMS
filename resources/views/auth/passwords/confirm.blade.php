@extends('layouts.app')

@section('content')
{{--
<div class="container d-none">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Confirm Password') }}</div>

                <div class="card-body">
                    {{ __('Please confirm your password before continuing.') }}

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Confirm Password') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<section class="background-radial-gradient overflow-hidden">
<style>
    .background-radial-gradient {
        background-color: hsl(142, 36%, 15%);
        background-image: radial-gradient(650px circle at 0% 0%,
            hsl(142, 36%, 35%) 15%,
            hsl(142, 36%, 30%) 35%,
            hsl(142, 36%, 20%) 75%,
            hsl(142, 36%, 19%) 80%,
            transparent 100%), 
            radial-gradient(1250px circle at 100% 100%,
            hsl(142, 36%, 45%) 15%,
            hsl(142, 36%, 30%) 35%,
            hsl(142, 36%, 20%) 75%,
            hsl(142, 36%, 19%) 80%,
            transparent 100%);
        height: 100vh;   
    }

    .bg-glass {
    background-color: hsla(0, 0%, 100%, 0.9) !important;
    backdrop-filter: saturate(200%) blur(25px);
    }

    .t-nav{
        display: none;
    }
</style>

<div class="container px-4 py-5 px-md-5 text-center text-lg-start my-5">
    <div class="row gx-lg-5 align-items-center mb-5">
    

    <div class="col-lg-6 mb-5 mb-lg-0 position-relative">
        <div class="card bg-glass">
            <div class="card-body px-4 py-5 px-md-5">
                <div class="card-header">{{ __('Confirm Password') }}</div>
                <div class="card-body">
                    {{ __('Please confirm your password before continuing.') }}

                    <form method="POST" action="{{ route('password.confirm') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Confirm Password') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>           
            </div>
        </div>
    </div>
    </div>
</div>
</section>
@endsection

<?php
/**
 * Created by PhpStorm.
 * User: BS108
 * Date: 10/9/2018
 * Time: 10:34 AM
 */
?>
@extends('layouts.login')

@section('content')
    <div class="login-form">
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="form-group row">
                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                <div class="col-md-8">
                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>

                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
            <div class="checkbox">
                <label>
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                    <a href="{{ route('login') }}">{{ __('Back to Login.') }}</a>
                </label>
            </div>
            <button type="submit" class="btn btn-success btn-flat m-b-30 m-t-30">
                {{ __('Send Password Reset Link') }}
            </button>
        </form>
    </div>
@endsection
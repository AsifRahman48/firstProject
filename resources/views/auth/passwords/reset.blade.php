@extends('layouts.app')

@section('stylesheets')
    <style>
        .field__rules {
            -moz-column-count: 1;
            column-count: 1;
            font-size: 0.8em;
            list-style: none;
            padding: 0 !important;
        }
        .field__rules > li {
            display: flex;
            align-items: center;
            padding: 3px 0;
            color: rgba(17, 17, 17, 0.6);
            transition: 0.2s;
        }
        .field__rules > li::before {
            content: '✔';
            display: inline-block;
            color: #d0d0d0;
            font-size: 1em;
            line-height: 0;
            margin: 0 6px 0 0;
            transition: 0.2s;
        }
        .field__rules > li.pass {
            color: #111;
        }
        .field__rules > li.pass::before {
            color: #2b93d9;
            text-shadow: 0 0 8px currentColor;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6 field">
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                                <ul class="field__rules">
                                    <li>One lowercase character</li>
                                    <li>One uppercase character</li>
                                    <li>One number</li>
                                    <li>One special character</li>
                                    <li>8 characters minimum</li>
                                </ul>
                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('add_script')
    <script src="{{ asset('ElaAdmin/assets/js/vendor/jquery-2.1.4.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(":password").on("input", onPasswordInput);

            function onPasswordInput(e) {
                const value = this.value;
                const rulesItems = $(this).closest(".field").find(".field__rules").find("li");
                const rules = {
                    "one lowercase character": /[a-z]/,
                    "one uppercase character": /[A-Z]/,
                    "one number": /[0-9]/,
                    "one special character": /[^a-z0-9]/i,
                    "8 characters minimum": /.{8,}/
                };

                this.classList.toggle("hasValue", this.value);

                rulesItems.each((i, elm) => {
                    let valid, rule = elm.innerText.toLowerCase();

                    if (rules[rule]) {
                        valid = new RegExp(rules[rule]).test(value);
                        elm.classList.toggle("pass", valid);
                    }
                });
            }
        })
    </script>
@endsection

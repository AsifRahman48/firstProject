
@extends('layouts.login')

@section('content')
    <div class="login-form" style="opacity: 0.91;">

         <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                @if(Session::has('message'))
                <div class="sufee-alert alert {{ Session::get('alert-class', 'alert-danger') }} with-close  alert-dismissible fade show">
                                       {{ Session::get('message') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                    @endif
                <label>{{ __('User Name') }}</label>
                <input id="email" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Username" name="email" value="{{ old('email') }}" required autofocus>

                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group">
                <label>{{ __('Password') }}</label>
                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Password" name="password" required>
           <!--      <input id="password" type="hidden" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="Password" value="123456" name="password" required> -->
                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
       <!--      <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    {{ __('Remember Me') }}
                </label>

            </div> -->
            <button type="submit" class="btn btn-dark btn-flat m-b-30 m-t-30">Sign in</button>

             @if(config('custom.settings.authentication') == 'database')
                 <div class="mb-3 text-center">
                     <small class="mb-2 text-center">Forget your Password <a href="{{ route('password.request') }}" class="ml-1 link">
                             <span class="text-black-50">Click Here</span>
                         </a>
                     </small>
                 </div>
             @endif
        </form>
    </div>
@endsection

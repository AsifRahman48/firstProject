@extends('layouts.elaadmin')

@section('stylesheets')
    <style>
        .field__rules {
            -moz-column-count: 1;
            column-count: 1;
            font-size: 0.8em;
            list-style: none;
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
    <div class="breadcrumbs">
        <div class="breadcrumbs-inner">
            <div class="row m-0">
                <div class="col-sm-4">
                    <div class="page-header float-left">
                        <div class="page-title">
                            <h1>{{ $data['pageTitle'] }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="page-header float-right">
                        <div class="page-title">
                            <ol class="breadcrumb text-right">
                                <li><a href="{{ url('/') }}">Dashboard</a></li>
                                <li><a href="{{ route("force.password_change.index") }}">{{ $data['pageTitle'] }}</a>
                                </li>
                                <li class="active">{{ $data['pageTitle'] }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <strong>{{ $data['pageTitle'] }}</strong>
                        </div>
                        <div class="card-body card-block ">
                            <div class="com-md-8  mx-auto">

                                {!! Form::open(['url'=> route('change.password.store'), 'class'=>'form-horizontal', 'role'=>'form', 'enctype'=>'multipart/form-data']) !!}
                                <div class="row form-group">
                                    <div class="col-12 col-md-12 d-md-flex">
                                        {!! Html::decode(Form::label('current_password', 'Current Password <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3' ])) !!}

                                        <div class="col-md-9">
                                            <input type="password" class="form-control" placeholder="Current Password"
                                                   required name="current_password">
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-12 col-md-12 d-md-flex">
                                        {!! Html::decode(Form::label('new_password', 'New Password <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3' ])) !!}

                                        <div class="col-md-9 field">
                                            <input type="password" class="form-control" pattern="^(?=.*[A-Z])(?=.*[!@#$&*])(?=.*[0-9])(?=.*[a-z]).{9,}$" placeholder="New Password" required name="new_password">
                                            <ul class="field__rules">
                                                <li>One lowercase character</li>
                                                <li>One uppercase character</li>
                                                <li>One number</li>
                                                <li>One special character</li>
                                                <li>8 characters minimum</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-12 col-md-12 d-md-flex">
                                        {!! Html::decode(Form::label('new_password_confirmation', 'Confirm Password <span class="mandatory-field">*</span>', ['class' => 'form-control-label col-md-3' ])) !!}

                                        <div class="col-md-9">
                                            <input type="password" class="form-control" placeholder="Confirm Password"
                                                   required name="new_password_confirmation">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions form-group text-center">
                                    {!! Form::button('Submit <i class="fa fa-forward"></i>', ['type' => 'submit', 'class' => 'btn btn-success btn-xs']) !!}
                                </div>
                                {!! Form::close() !!}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('add_script')
    <script>
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
    </script>
@endsection

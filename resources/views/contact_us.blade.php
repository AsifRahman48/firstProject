<?php
/**
 * Created by PhpStorm.
 * User: BS108
 * Date: 10/10/2018
 * Time: 5:58 PM
 */
?>
@extends('layouts.elaadmin')

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
                              
                                <li class="active">{{ $data['pageTitle'] }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
<div class="card">
  <div class="card-body">
    <h5 class="card-title">Contact us</h5>
		<p class="card-text">Name : Md. Taskinur Rahman <br>
		Designation: Strategic Business Unit Head <br>
		Phone No:+88-02-9856728,<br>
		Call No: +8801722361016,<br>
		Email: taskinur@brainstation-23.com<br>
		</p>
    
  </div>
</div>

  </div>

  @endsection
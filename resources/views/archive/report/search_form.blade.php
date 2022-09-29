<div class="col-md-12" style="padding: 0px;">
	<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
    <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Report Search</strong>
                            </div>
                            <div class="card-body">
                                <!-- Credit Card -->
                                <div id="pay-invoice">
                                    <div class="card-body">
                                        <div class="card-title">
                                            <!-- <h3 class="text-center">Pay Invoice</h3> -->
                                        </div>
                                        <!-- <hr> -->
                                        <form id="searchData"  method="post" novalidate="novalidate">
 
  <div class="row" style="margin-bottom: 10px;">
    <!-- <input type="text" name="csrf-token" value="{{ csrf_token() }}"> -->
                                                <div class="col-md-6 ">
                                                    <div class="form-group d-flex">
                                                       {!! Html::decode(Form::label('Start Date', 'Start Date', ['class' => 'form-control-label col-md-4'])) !!}
                                                        <input id="start_date" name="start_date" type="text" class="form-control datepicker col-md-8" autocomplete="off" placeholder="Start Date">
                                                        <span class="help-block" data-valmsg-for="cc-exp" data-valmsg-replace="true"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group d-flex">
                                                       {!! Html::decode(Form::label('End Date', 'End Date', ['class' => 'form-control-label col-md-4'])) !!}
                                                        <input id="end_date" name="end_date" type="text" class="form-control datepicker col-md-8" autocomplete="off"  placeholder="End Date">
                                                        <span class="help-block" data-valmsg-for="cc-exp" data-valmsg-replace="true"></span>
                                                    </div>
                                                </div>

                                            


                                            </div>

                                            <div class="row" style="margin-bottom: 10px;">
                                                <div class="col-md-6 ">
                                                    <div class="form-group d-flex">
                                                       {!! Html::decode(Form::label('cat_id', 'Department', ['class' => 'form-control-label col-md-4'])) !!}
                                                       {!! Form::select('cat_id', $data['catList'], old('cat_id'), ['id' => 'select_cat', 'placeholder' => 'Select Department', 'class' => 'form-control custom-select col-md-8', 'required' => 'required']) !!}

                                        @if($errors->has('cat_id'))
                                            <small class="help-block form-text">{{ $errors->first('cat_id') }}</small>
                                        @endif

                                         
                                                    </div>
                                                     
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group d-flex">
                                                       {!! Html::decode(Form::label('Sub-Category', 'Unit/Section', ['class' => 'form-control-label col-md-4'])) !!}
                                                        <select id="select_sub_cat" class="form-control custom-select col-md-8" required="required" name="sub_cat_id" onchange="errorHide()">
                                            <option selected="selected" value="">Select Department first...</option>
                                        </select>
                                                    </div>
                                                </div>

                                            


                                            </div>


                                <div class="row">
                                         <!--    <div class="col-md-4">
                                                   
                                                    <div class="input-group d-flex">
                                                          {!! Html::decode(Form::label('Statuss', 'Statuss', ['class' => 'form-control-label col-md-4'])) !!}
                                                         {!! Form::select('status', ['2'=>'Pending','4'=>'Approved','5'=>'Rejected','6'=>'Request Info'], old('status'), ['placeholder' => 'Select Status', 'class' => 'form-control col-md-8']) !!}

                                  
                                                       
                                                        
                                                    </div>
                                                </div>  -->                        
                                                <div class="col-md-6">
                                                    <div class="form-group d-flex">
                                                       {!! Html::decode(Form::label('Start Date', 'Search Text', ['class' => 'form-control-label col-md-4'])) !!}
                                                        <input id="textSerch" name="textSerch" type="text" class="form-control col-md-8" placeholder="Text Serch">
                                                    
                                                    </div>
                                                </div>

                                                  <div class="col-md-6">
                                                   
                                                 <button id="payment-button" type="submit" class="btn btn-md btn-info pull-right">
                                                    <i class="fa fa-search fa-lg"></i>&nbsp;
                                                    <span id="payment-button-amount">Search</span>
                                                    <span id="payment-button-sending" style="display:none;">Sending…</span>
                                                </button>
                                                </div>
                                            
                                            </div>
                                           
                                    
                                            <div>
                                               
                                            </div>
                                    </form>
                                    </div>
                                </div>

                            </div>
                        </div> 

	


            <script>
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
  function errorHide(){
 $("#cat_error").hide();
    }

    /**
         * Change Category
         */
        $("#select_cat").change(function (e) {
            e.preventDefault();
            var category = $("select[name=cat_id]").val();
            if(category === ''){
                $("#cat_error").css({"display": "block", "color": "red"});
                $("#cat_error").html('Please select a Department.');
                $('#select_sub_cat').empty();
                $('#select_sub_cat').append('<option value="">Select Department first...</option>');

            }else{
                $("#cat_error").css({"display": "block", "color": "green"});
                $("#cat_error").html('Now please select a Unit/Section.');

                $.ajax({
                    type: 'POST',
                    url: "{{ URL::to('/get_sub_cat')}}",
                    data: {"_token": $('meta[name = csrf-token]').attr('content'),"cat_id" : category},
                    datatype: 'json',
                    statusCode:{
                        400:function(data){
                            $("#cat_error").css({"display":"block", "color":"red"});
                            $("#cat_error").html('Please select a Department.');
                        }
                    },
                    success: function (data) {
                        $('#select_sub_cat').empty();
                        $('#select_sub_cat').append('<option value="">Select Unit/Section...</option>');
                        $.each(data.data, function(key, value){
                            $('#select_sub_cat').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                        $("#cat_error").css({"display":"block", "color":"green"});
                        $("#cat_error").html('Now please select a Unit/Section .');
                    }
                });
            }
        });

$(".datepicker" ).datepicker({
    dateFormat: 'dd-mm-yy',//check change
    changeMonth: true,
    changeYear: true
});


$(document).ready(function(){
    $("form#searchData").submit(function(event) {
        event.preventDefault();
        var inputfromdata=$('#searchData').serialize();
        var start_date=$('input[name="start_date"]').val();
        var end_date=$('input[name="end_date"]').val();
        var cat_id=$('#select_cat').val();
        var sub_cat_id=$('#select_sub_cat').val();
        // var status=$('input[name="status"]').val();
        var textSerch=$('input[name="textSerch"]').val();
        var meta=$('meta[name = csrf-token]').attr('content');
       
        // alert(sub_cat_id);
        $.ajax({
            // async: false,
             url: "{{ URL::to('/archive_report_search')}}",
            type: "POST",
            data:  {"_token":meta ,"start_date":start_date,"end_date":end_date,"cat_id":cat_id,"sub_cat_id":sub_cat_id,"textSerch":textSerch},
            beforeSend: function(){
                   $('#loader').removeClass('d-none');
                       $('#loader').show();
                   },
            success: function(data){
                // alert('success');
                $('#reportView').empty();
                $('#reportView').append(data);
                 $('#loader').hide();
                 
            }
        });
    });
});

  </script>
	
</div>
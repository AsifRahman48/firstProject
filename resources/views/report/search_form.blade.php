<div class="col-md-12" style="padding: 0px;">
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <div class="card">
                            <div class="card-header">
                                <strong class="card-title">Report Search</strong>
                            </div>
                            <div class="card-body">
                                <div id="pay-invoice">
                                    <div class="card-body">
                                        <div class="card-title">
                                        </div>
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
                                                <div class="col-md-6 col-sm-12 ">
                                                    <div class="form-group d-flex">
                                                       {!! Html::decode(Form::label('cat_id', 'Department ', ['class' => 'form-control-label col-md-4'])) !!}
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


                                 <div class="row" style="margin-bottom: 10px;">
                                            <div class="col-md-6 col-sm-12">         
                                                <div class="form-group d-flex">
                                                          {!! Html::decode(Form::label('Status', 'Status', ['class' => 'form-control-label col-md-4'])) !!}


                                                         {!! Form::select('status', ['4'=>'Approved','5'=>'Rejected'], old('status'), ['placeholder' => 'Select Status', 'class' => 'form-control col-md-8 custom-select','id'=>'status']) !!}

                                  
                                                       
                                                        
                                                    </div>
                                                </div>                         
                                                <div class="col-md-6">
                                                    <div class="form-group d-flex">
                                                       {!! Html::decode(Form::label('Search Text', 'Search Text', ['class' => 'form-control-label col-md-4'])) !!}
                                                        <input id="textSerch" name="textSerch" type="text" class="form-control col-md-8" placeholder="Text Serch">
                                                    
                                                    </div>
                                                </div>
                                    <div class="col-md-12">
                                       <!-- <div class="form-group d-flex"> -->
                                     <!--   <div class="col-md-2">
                                    Search For  
                                            </div> -->
                                    <!-- <div class="col-md-8" style=" display: none"> -->
                                          <!-- Default inline 1-->
                                        <!-- <div class="custom-control custom-radio custom-control-inline" style=" display: none"> -->
                                          <input type="radio" value="1" accept=" s" class="custom-control-input" id="defaultInline1" name="searchType">
                                          <!-- <label class="custom-control-label"  for="defaultInline1">Initiat Request</label>
                                        </div> -->

                                        <!-- Default inline 2-->
                                       <!--  <div class="custom-control custom-radio custom-control-inline">
                                          <input type="radio" value="2" class="custom-control-input" id="defaultInline2" name="searchType">
                                          <label class="custom-control-label" for="defaultInline2">Recommendation/Approval Reques</label>
                                        </div> -->

                                        <!-- </div> -->
                                          <div class="col-md-2 pull-right">
   
                                                 <button id="payment-button" type="submit" class="btn btn-md btn-info">
                                                    <i class="fa fa-search fa-lg"></i>&nbsp;
                                                    <span id="payment-button-amount">Search</span>
                                                    <!-- <span id="payment-button-sending" style="display:none;">Search</span> -->
                                                </button>
                                                </div>
                                                </div>
                                            
                                            <!-- </div> -->
                                            </div>
                                           
                                    
                                            <div>
                                               
                                            </div>
                                    </form>
                                    </div>
                                </div>

                            </div>
                        </div> 

	


            <script>
radiobtn = document.getElementById("defaultInline1");
radiobtn.checked = true;


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

   var $datepicker = $('.datepicker');
$datepicker.datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true,
    changeYear: true });
$datepicker.datepicker('setDate', new Date());

$('.datepicker2').datepicker({ dateFormat: 'dd-mm-yy', changeMonth: true,
    changeYear: true }).val();


$(document).ready(function(){
    $("form#searchData").submit(function(event) {
        event.preventDefault();
        var inputfromdata=$('#searchData').serialize();
        var start_date=$('input[name="start_date"]').val();
        var end_date=$('input[name="end_date"]').val();
        var cat_id=$('#select_cat').val();
        var sub_cat_id=$('#select_sub_cat').val();
        var status=$('#status').val();
        var textSerch=$('input[name="textSerch"]').val();
        var searchType=$('input[name="searchType"]:checked').val();
        var meta=$('meta[name = csrf-token]').attr('content');
       
        // alert(sub_cat_id);
        $.ajax({
            // async: false,
             url: "{{ URL::to('/report_search')}}",
            type: "POST",
            data:  {"_token":meta ,"start_date":start_date,"end_date":end_date,"cat_id":cat_id,"sub_cat_id":sub_cat_id,"status":status,"textSerch":textSerch,"searchType":searchType},
            beforeSend: function(){
                       $('#loader').show();
                       $('#loader').removeClass('d-none');
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
<div class="col-md-12" style="padding: 0px;">
    <div class="card">
        <div class="card-header">
            <strong class="card-title">Subordinate Users Report</strong>
        </div>
        <div class="card-body">
            <div id="pay-invoice">
                <div class="card-body">
                    <form id="searchData" method="post" novalidate="novalidate">

                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-6 ">
                                <div class="form-group d-flex">
                                    {!! Html::decode(Form::label('Start Date', 'Start Date', ['class' => 'form-control-label col-md-4'])) !!}
                                    <input id="start_date" name="start_date" type="text"
                                           class="form-control datepicker col-md-8" autocomplete="off"
                                           placeholder="Start Date"
                                           value="{{ \Carbon\Carbon::now()->subDays(30)->format('d-m-Y') }}">
                                    <span class="help-block" data-valmsg-for="cc-exp" data-valmsg-replace="true"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group d-flex">
                                    {!! Html::decode(Form::label('End Date', 'End Date', ['class' => 'form-control-label col-md-4'])) !!}
                                    <input id="end_date" name="end_date" type="text"
                                           class="form-control datepicker col-md-8" autocomplete="off"
                                           value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}"
                                           placeholder="End Date">
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
                                    <select id="select_sub_cat" class="form-control custom-select col-md-8"
                                            required="required" name="sub_cat_id" onchange="errorHide()">
                                        <option selected="selected" value="">Select Department first...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom: 10px;">
                            <div class="col-md-6 col-sm-12">
                                <div class="form-group d-flex">
                                    {!! Html::decode(Form::label('Company', 'Company', ['class' => 'form-control-label col-md-4'])) !!}
                                    {!! Form::select('company_id', $data['comList'], old('company_id'), ['placeholder' => 'Select Company', 'class' => 'form-control col-md-8 custom-select','id'=>'company_id']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group d-flex">
                                    {!! Html::decode(Form::label('Search Text', 'Search Text', ['class' => 'form-control-label col-md-4'])) !!}
                                    <input id="textSerch" name="textSerch" type="text" class="form-control col-md-8"
                                           placeholder="Text Serch">
                                </div>
                            </div>
                            <div class="col-md-6 mt-1">
                                <div class="form-group d-flex">
                                    {!! Html::decode(Form::label('Reference No / Ticket ID', null, ['class' => 'form-control-label col-md-4'])) !!}
                                    <input id="reference_no" name="reference_no" type="text"
                                           class="form-control col-md-8 mt-1" placeholder="Reference No / Ticket ID">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group d-flex">
                                    {!! Html::decode(Form::label('Initiated By', 'Initiated By', ['class' => 'form-control-label col-md-4'])) !!}
                                    <select id="initiated_by" name="initiated_by" class="form-control select2" required>
                                        <option value="">Initiated By</option>
                                        @foreach($subordinate_users_list as $user)
                                            <option value="{{ $user->id }}">{{ "$user->name > $user->email" }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-2 pull-right">
                                    <button id="payment-button" type="submit" class="btn btn-md btn-info">
                                        <i class="fa fa-search fa-lg"></i>&nbsp;
                                        <span id="payment-button-amount">Search</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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

    function errorHide() {
        $("#cat_error").hide();
    }

    /**
     * Change Category
     */
    $("#select_cat").change(function (e) {
        e.preventDefault();
        var category = $("select[name=cat_id]").val();
        if (category === '') {
            $("#cat_error").css({"display": "block", "color": "red"});
            $("#cat_error").html('Please select a Department.');
            $('#select_sub_cat').empty();
            $('#select_sub_cat').append('<option value="">Select Department first...</option>');

        } else {
            $("#cat_error").css({"display": "block", "color": "green"});
            $("#cat_error").html('Now please select a Unit/Section.');

            $.ajax({
                type: 'POST',
                url: "{{ URL::to('/get_sub_cat')}}",
                data: {"_token": $('meta[name = csrf-token]').attr('content'), "cat_id": category},
                datatype: 'json',
                statusCode: {
                    400: function (data) {
                        $("#cat_error").css({"display": "block", "color": "red"});
                        $("#cat_error").html('Please select a Department.');
                    }
                },
                success: function (data) {
                    $('#select_sub_cat').empty();
                    $('#select_sub_cat').append('<option value="">Select Unit/Section...</option>');
                    $.each(data.data, function (key, value) {
                        $('#select_sub_cat').append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    $("#cat_error").css({"display": "block", "color": "green"});
                    $("#cat_error").html('Now please select a Unit/Section.');
                }
            });
        }
    });

    var $datepicker = $('.datepicker');
    $datepicker.datepicker({
        dateFormat: 'dd-mm-yy', changeMonth: true,
        changeYear: true
    });
    // $datepicker.datepicker('setDate', new Date());

    $('.datepicker2').datepicker({dateFormat: 'dd-mm-yy', changeMonth: true, changeYear: true}).val();

    $(document).ready(function () {
        $("form#searchData").submit(function (event) {
            event.preventDefault();
            var start_date = $('input[name="start_date"]').val();
            var end_date = $('input[name="end_date"]').val();
            var cat_id = $('#select_cat').val();
            var sub_cat_id = $('#select_sub_cat').val();
            var status = $('#status').val();
            var company_id = $('#company_id').val();
            var textSerch = $('input[name="textSerch"]').val();
            var reference_no = $('input[name="reference_no"]').val();
            var meta = $('meta[name = csrf-token]').attr('content');
            var initiated_by = $('#initiated_by').find(':selected').val();

            $.ajax({
                url: "{{ URL::to('/report_search')}}",
                type: "POST",
                data: {
                    "_token": meta,
                    "is_subordinate": "yes",
                    "start_date": start_date,
                    "end_date": end_date,
                    "cat_id": cat_id,
                    "sub_cat_id": sub_cat_id,
                    "status": status,
                    "company_id": company_id,
                    "textSerch": textSerch,
                    "reference_no": reference_no,
                    "searchType": 3,
                    "initiated_by": initiated_by
                },
                beforeSend: function () {
                    $('#loader').show();
                    $('#loader').removeClass('d-none');
                },
                success: function (data) {
                    $('#reportView').empty();
                    $('#reportView').append(data);
                    $('#loader').hide();
                }
            });
        });
    });

</script>

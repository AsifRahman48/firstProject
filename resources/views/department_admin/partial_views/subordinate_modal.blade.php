<style>
    .select2-container, select2-search__field {
        width: 100% !important;
    }
</style>
<div id="addSubordinateUsers" class="modal fade" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">User List</h4>
            </div>

            {!! Form::open(['url'=> route('subordinate.users.store'), 'class'=>'form-horizontal', 'role'=>'form', 'name'=>'requestAddPost', 'enctype'=>'multipart/form-data','method' => 'post']) !!}

            <div class="modal-body">
                <input type="hidden" name="user_id" id="parentUserID">

                {!! Form::select('subordinate_users[]', [], old('subordinate_users'), ['id' => 'subordinate_users', 'class' => 'form-control subordinate-select2', 'required' => 'required', 'multiple' => 'multiple']) !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save</button>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>

<!-- Select 2 Library -->
<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        $(".subordinate-select2").select2({
            placeholder: "Select User",
            width: '100%',
            closeOnSelect: false,
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('subordinate.users.search') }}",
                dataType: 'json',
                delay: 250,
                data: function (query) {
                    let parentUserID = {'parentUserID': $("#parentUserID").val()};
                    // return query;
                    return {...query, ...parentUserID};
                },
                processResults: function (data) {
                    var results = [];
                    data.forEach(e => {
                        let userInfo = e.name + '->' + e.email;
                        results.push({id: e.id, text: userInfo});
                    });

                    return {
                        results: results
                    };
                },
            },
            templateResult: formatResult
        });
    });

    function formatResult(d) {
        if (d.loading) {
            return d.text;
        }
        // Creating an option of each id and text
        $d = $('<option/>').attr({'value': d.value}).text(d.text);
        return $d;
    }
</script>

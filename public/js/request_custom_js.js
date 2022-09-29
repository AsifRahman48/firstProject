


        /**
         * Change Category
         */
        $("#select_cat").change(function (e) {
            e.preventDefault();
            var category = $("select[name=cat_id]").val();
            if(category === ''){
                $("#cat_error").css({"display": "block", "color": "red"});
                $("#cat_error").html('Please select a category.');
                $('#select_sub_cat').empty();
                $('#select_sub_cat').append('<option value="">Select category first...</option>');

            }else{
                $("#cat_error").css({"display": "block", "color": "green"});
                $("#cat_error").html('You choose a category, Now please select a sub category below.');

                $.ajax({
                    type: 'POST',
                    url: "{{ URL::to('/get_sub_cat')}}",
                    data: {cat_id : category},
                    datatype: 'json',
                    statusCode:{
                        400:function(data){
                            $("#cat_error").css({"display":"block", "color":"red"});
                            $("#cat_error").html('Please select a category.');
                        }
                    },
                    success: function (data) {
                        $('#select_sub_cat').empty();
                        $('#select_sub_cat').append('<option value="">Select sub-category...</option>');
                        $.each(data.data, function(key, value){
                            $('#select_sub_cat').append('<option value="'+value.id+'">'+value.name+'</option>');
                        });
                        $("#cat_error").css({"display":"block", "color":"green"});
                        $("#cat_error").html('You choose a category, Now please select a sub category below.');
                    }
                });
            }
        });



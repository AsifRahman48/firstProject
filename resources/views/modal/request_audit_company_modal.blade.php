<?php
/**
 * Created by PhpStorm.
 * User: BS108
 * Date: 2/24/2019
 * Time: 5:19 PM
 */
?>
<style>
#auditSelectCompany .select2-container{
    width: 100% !important;
}
#auditSelectCompany .select2-container .select2-search__field{
    width: 100% !important;
}
</style>
<div id="auditSelectCompany" class="modal fade" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Company List</h4>
            </div>

            {!! Form::open(['url'=>'audit_company_post', 'class'=>'form-horizontal', 'role'=>'form', 'name'=>'requestAddPost', 'enctype'=>'multipart/form-data']) !!}
            <div class="modal-body">
                <div class="row form-group">
                    <input type="hidden" name="user_id" id="auditCompanyUserID">
                    <div class="col-12 col-md-12">
                        {!! Html::decode(Form::label('CompanyName', 'Company Name <span class="mandatory-field">*</span>', ['class' => 'form-control-label'])) !!}
                    </div>
                    <div class="col-12 col-md-12">
                        {!! Form::select('company_ids[]', $data['CompanyName'], old('company_ids'), ['id' => 'company_ids', 'class' => 'form-control audit-company-select2', 'required' => 'required', 'multiple' => 'multiple']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Save changes</button>
            </div>
            {!! Form::close() !!}

        </div>
    </div>
</div>

<!-- Select 2 Library -->
<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js"></script>

<script>
$(document).ready(function(){
    $(".audit-company-select2").select2({
        placeholder: "Select Company Name"
    });
});
</script>
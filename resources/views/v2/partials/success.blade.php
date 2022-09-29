@if (session('status'))
    <div class="sufee-alert alert with-close alert-success alert-dismissible fade show no-margin">
        <span class="badge badge-pill badge-success">Success</span>
        {{ session('status') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
@endif

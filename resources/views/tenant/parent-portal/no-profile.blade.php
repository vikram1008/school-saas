@extends('layouts.tenant')

@section('title', 'Parent Portal')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="icon-base ti tabler-user-off"
               style="font-size:4rem; color:#ccc;"></i>
            <h5 class="mt-3 mb-2">No Profile Found</h5>
            <p class="text-muted mb-0">
                Your account is not linked to any parent profile.<br>
                Please contact the school administration.
            </p>
        </div>
    </div>
</div>
@endsection
@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Create Warehouse <small>Add a new warehouse.</small></h1>
        </div>
        <!-- END PAGE TITLE -->
    </div>
    <!-- END PAGE HEAD -->
    <!-- BEGIN PAGE BREADCRUMB -->
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Shipping</span>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ route('admin.warehouses.index') }}">Warehouses</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Create</span>
        </li>
    </ul>
    <!-- END PAGE BREADCRUMB -->
    <!-- END PAGE HEADER-->
    <!-- BEGIN PAGE CONTENT-->
    <div class="row">
        <div class="col-md-12 ">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-plane font-blue-hoki"></i>
                        <span class="caption-subject font-blue-hoki bold uppercase">Create Warehouse</span>
                    </div>
                    <div class="actions">
                        <a href="{{ route('admin.warehouses.index') }}" class="btn btn-circle btn-default btn-sm" data-toggle="tooltip" title="Back to Warehouse List">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="" title="">
                        </a>
                    </div>
                </div>
                <div class="portlet-body form">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.warehouses.store') }}" role="form" method="POST">
                        @csrf
                        @include('admin.warehouses._form', ['warehouse' => null])
                        <div class="form-actions right">
                            <a href="{{ route('admin.warehouses.index') }}" class="btn default">Cancel</a>
                            <button type="submit" class="btn green">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END SAMPLE FORM PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <div class="page-head">
        <div class="page-title">
            <h1>Edit Size Chart <small>Update the measurements of this size chart.</small></h1>
        </div>
    </div>
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ route('admin.size-charts.index') }}">Size Charts</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <span>Edit</span>
        </li>
    </ul>
    <!-- END PAGE HEADER-->
    <!-- BEGIN PAGE CONTENT-->
    <div class="row">
        <div class="col-md-12 ">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-table font-blue-hoki"></i>
                        <span class="caption-subject font-blue-hoki bold uppercase">Edit Size Chart</span>
                    </div>
                    <div class="actions">
                        <a href="{{ route('admin.size-charts.index') }}" class="btn btn-circle btn-default btn-sm" data-toggle="tooltip" title="Back to Size Chart List">
                            <i class="fa fa-arrow-left"></i> Back
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
                    <form action="{{ route('admin.size-charts.update', $sizeChart) }}" role="form" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('admin.size-charts._form', ['sizeChart' => $sizeChart, 'defaultSizes' => []])
                        <div class="form-actions right">
                            <a href="{{ route('admin.size-charts.index') }}" class="btn default">Cancel</a>
                            <button type="submit" class="btn green">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

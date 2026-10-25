@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Role Management <small>Manage administrator roles and their access.</small></h1>
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
            <a href="{{ route('admin.roles.index') }}">Role Management</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="javascript:;">Create Role</a>
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
                        <i class="icon-equalizer font-blue-hoki"></i>
                        <span class="caption-subject font-blue-hoki bold uppercase">Create Role Management</span>
                    </div>
                    <div class="tools">
                        <a href="" class="collapse">
                        </a>
                        <a href="" class="reload">
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
                    <form action="{{ route('admin.roles.store') }}" class="" role="form" method="POST">
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Role Name</label>
                                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Inventory Manager" required>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <!-- <div class="form-group">
                                        <label class="control-label">Last Name</label>
                                        <input type="text" id="lastName" class="form-control" placeholder="Lim">
                                        <span class="help-block">This field has error. </span>
                                    </div> -->
                                </div>
                                <!--/span-->
                            </div>
                            <!--/row-->
                            <h3 class="form-section">Assign Permissions</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    @foreach($permissions as $module => $modulePermissions)
                                        <div class="form-group">
                                            <label class="control-label">{{ str_replace('_', ' ', $module) }}</label>
                                            <div class="checkbox-list">
                                                @foreach($modulePermissions as $permission)
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, old('permissions', [])))>
                                                        {{ $permission->name }}
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <!--/row-->
                        </div>
                        <div class="form-actions right">
                            <button type="button" class="btn default">Cancel</button>
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
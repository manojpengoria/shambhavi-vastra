@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Edit Role Management <small>Manage administrator roles and their access.</small></h1>
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
            <a href="javascript:;">Edit Role</a>
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
                        <span class="caption-subject font-blue-hoki bold uppercase">Edit Role Management</span>
                    </div>
                    <div class="tools">
                        <a href="" class="collapse">
                        </a>
                        <a href="" class="reload">
                        </a>
                    </div>
                </div>
                <div class="portlet-body form">
                    {{-- Success/Error Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <strong>Please correct the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.roles.update', $role) }}" class="" role="form" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Role Name <span class="text-danger">*</span></label>
                                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $role->name) }}" placeholder="e.g. Inventory Manager" maxlength="100" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                    <div class="form-control height-auto">
                                        <div class="" style="data-always-visible="1">
                                            @forelse ($permissions as $group => $groupPermissions)
                                            <ul class="list-unstyled permission-group">
                                                <li>
                                                    <label for="group_{{ $group }}">
                                                        <input type="checkbox" class="form-check-input group-toggle" id="group_{{ $group }}">{{ str_replace('_', ' ', ucfirst($group)) }}
                                                    </label>
                                                    <ul class="list-unstyled">
                                                        @foreach($groupPermissions as $permission)
                                                        <li>
                                                            <label>
                                                                <input type="checkbox" class="form-check-input permission-checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->name }}" @checked(in_array($permission->name, old('permissions', $role->permissions->pluck('name')->all()), true))>{{ $permission->name }}
                                                            </label>
                                                        </li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            </ul>
                                            @empty
                                                <div class="alert alert-warning mb-0">
                                                    No admin permissions found. Please run your permission seeder.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const permissionCheckboxes = [
            ...document.querySelectorAll('.permission-checkbox')
        ];

        const groupToggles = [
            ...document.querySelectorAll('.group-toggle')
        ];

        function updateGroupState(group) {
            const container = group.closest('.permission-group');
            const checkboxes = [
                ...container.querySelectorAll('.permission-checkbox')
            ];

            const checkedCount = checkboxes.filter(
                checkbox => checkbox.checked
            ).length;

            group.checked = checkedCount === checkboxes.length;
            group.indeterminate =
                checkedCount > 0 && checkedCount < checkboxes.length;
        }

        groupToggles.forEach(function (group) {
            group.addEventListener('change', function () {
                const container = group.closest('.permission-group');
                const checkboxes = container.querySelectorAll('.permission-checkbox');
                checkboxes
                    .forEach(function (checkbox) {
                        checkbox.checked = group.checked;
                    });
                App.updateUniform(checkboxes);
            });
            updateGroupState(group);
        });

        permissionCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const container = checkbox.closest('.permission-group');
                const group = container.querySelector('.group-toggle');
                updateGroupState(group);
                App.updateUniform(group);
            });
        });
    });
</script>
@endsection
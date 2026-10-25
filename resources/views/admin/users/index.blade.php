@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Admin User Management</h1>
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
            <a href="{{ route('admin.users.index') }}">Users</a>
        </li>
    </ul>
    <!-- END PAGE BREADCRUMB -->
    <!-- END PAGE HEADER-->
    <!-- BEGIN PAGE CONTENT-->
    <div class="row">
        <div class="col-md-12">
            <!-- BEGIN EXAMPLE TABLE PORTLET-->
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-cogs"></i>Admin User Management
                    </div>
                    @can('users.create')
                    <div class="actions">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-warning btn-md pointer">
                        <i class="fa fa-plus"></i> Add New User</a>
                    </div>
                    @endcan
                </div>
                <div class="portlet-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <table class="table table-striped table-bordered" id="sample_1">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Assigned Roles</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr class="odd gradeX">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->mobile }}</td>
                            <td>{{ $user->roles->first()->name ?? 'No Role Assigned' }}</td>
                            <td>
                                @can('users.edit')
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="btn btn-icon-only purple">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endcan
                                @can('users.delete')
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon-only red" onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                    </table>
                </div>
                {{ $users->links() }}
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

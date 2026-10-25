@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Warehouses</h1>
            <small>Manage pickup warehouses.</small>
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
                        <i class="fa fa-truck"></i>Warehouse List
                    </div>
                    @can('warehouses.create')
                    <div class="actions">
                        <a href="{{ route('admin.warehouses.create') }}" class="btn btn-warning btn-md pointer">
                        <i class="fa fa-plus"></i> Add New Warehouse</a>
                    </div>
                    @endcan
                </div>
                <div class="portlet-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            {{ session('error') }}
                        </div>
                    @endif
                    <form method="GET" action="{{ route('admin.warehouses.index') }}" class="margin-bottom-20">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search name, code, contact or mobile">
                            </div>
                            <div class="col-md-2">
                                <select name="is_active" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="1" @selected(request('is_active') === '1')>Active</option>
                                    <option value="0" @selected(request('is_active') === '0')>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn blue"><i class="fa fa-filter"></i> Filter</button>
                                <a href="{{ route('admin.warehouses.index') }}" class="btn default">Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-scrollable">
                    <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Default</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                        <tbody>
                        @forelse($warehouses as $item)
                            <tr class="odd gradeX">
                                <td>{{ $warehouses->firstItem() + $loop->index }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ $item->contact_person }}<br><small class="text-muted">{{ $item->mobile }}</small></td>
                                <td>{{ $item->city->city_name }}, {{ $item->state->state_name }}<br><small class="text-muted">{{ $item->postalCode->postal_code }}</small></td>
                                <td>
                                    @if ($item->is_default)
                                        <span class="badge bg-success">Default</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @can('warehouses.edit')
                                        <a href="{{ route('admin.warehouses.edit', $item) }}" class="btn btn-icon-only purple">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('warehouses.delete')
                                        <form action="{{ route('admin.warehouses.destroy', $item) }}" method="POST" class="d-inline" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon-only red" onclick="return confirm('Are you sure you want to delete this warehouse?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No warehouses found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                    </div>
                </div>
                {{ $warehouses->links() }}
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

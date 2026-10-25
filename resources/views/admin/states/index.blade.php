@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>States</h1>
            <small>Manage states and union territories.</small>
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
            <span>Location Management</span>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ route('admin.states.index') }}">States</a>
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
                        <i class="fa fa-map-marker"></i>State List
                    </div>
                    @can('states.create')
                    <div class="actions">
                        <a href="{{ route('admin.states.create') }}" class="btn btn-warning btn-md pointer">
                        <i class="fa fa-plus"></i> Add New State</a>
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
                    <form method="GET" action="{{ route('admin.states.index') }}" class="margin-bottom-20">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search name, code or capital">
                            </div>
                            <div class="col-md-3">
                                <select name="country_id" class="form-control">
                                    <option value="">All Countries</option>
                                    @foreach($countries as $option)
                                        <option value="{{ $option->id }}" @selected(request('country_id') === (string) $option->id)>{{ $option->country_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="state_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="STATE" @selected(request('state_type') === 'STATE')>State</option>
                                    <option value="UT" @selected(request('state_type') === 'UT')>Union Territory</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="707" @selected(request('status') === '707')>Active</option>
                                    <option value="505" @selected(request('status') === '505')>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn blue"><i class="fa fa-filter"></i> Filter</button>
                                <a href="{{ route('admin.states.index') }}" class="btn default">Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-scrollable">
                    <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Country</th>
                        <th>State Name</th>
                        <th>Code</th>
                        <th>GST Code</th>
                        <th>Type</th>
                        <th>Capital</th>
                        <th>Cities</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                        <tbody>
                        @forelse($states as $item)
                            <tr class="odd gradeX">
                                <td>{{ $states->firstItem() + $loop->index }}</td>
                                <td>{{ $item->country->country_name }}</td>
                                <td>{{ $item->state_name }}</td>
                                <td>{{ $item->state_code }}</td>
                                <td>{{ $item->gst_state_code ?? '—' }}</td>
                                <td>{{ $item->state_type === 'UT' ? 'Union Territory' : 'State' }}</td>
                                <td>{{ $item->capital ?? '—' }}</td>
                                <td>{{ $item->cities_count }}</td>
                                <td>
                                    @if ($item->status === 707)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $item->sort_order }}</td>
                                <td>
                                    @can('states.edit')
                                        <a href="{{ route('admin.states.edit', $item) }}" class="btn btn-icon-only purple">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('states.delete')
                                        <form action="{{ route('admin.states.destroy', $item) }}" method="POST" class="d-inline" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon-only red" onclick="return confirm('Are you sure you want to delete this state?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    No states found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                    </div>
                </div>
                {{ $states->links() }}
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Areas</h1>
            <small>Manage areas, localities and their delivery availability.</small>
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
            <a href="{{ route('admin.areas.index') }}">Areas</a>
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
                        <i class="fa fa-map-marker"></i>Area List
                    </div>
                    @can('areas.create')
                    <div class="actions">
                        <a href="{{ route('admin.areas.create') }}" class="btn btn-warning btn-md pointer">
                        <i class="fa fa-plus"></i> Add New Area</a>
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
                    <form method="GET" action="{{ route('admin.areas.index') }}" class="margin-bottom-20">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search name or code">
                            </div>
                            <div class="col-md-3">
                                <select name="city_id" class="form-control js-location-select" data-lookup-url="{{ route('admin.locations.lookup.cities') }}" data-placeholder="All Cities" data-include-disabled="1">
                                    <option value=""></option>
                                    @if($selectedCity)
                                        <option value="{{ $selectedCity->id }}" selected>{{ $selectedCity->selectLabel() }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="area_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="LOCALITY" @selected(request('area_type') === 'LOCALITY')>Locality</option>
                                    <option value="COLONY" @selected(request('area_type') === 'COLONY')>Colony</option>
                                    <option value="SECTOR" @selected(request('area_type') === 'SECTOR')>Sector</option>
                                    <option value="VILLAGE" @selected(request('area_type') === 'VILLAGE')>Village</option>
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
                                <a href="{{ route('admin.areas.index') }}" class="btn default">Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-scrollable">
                    <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>City</th>
                        <th>Area Name</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Postal Code</th>
                        <th>Delivery</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                        <tbody>
                        @forelse($areas as $item)
                            <tr class="odd gradeX">
                                <td>{{ $areas->firstItem() + $loop->index }}</td>
                                <td>{{ $item->city->city_name }}</td>
                                <td>{{ $item->area_name }}</td>
                                <td>{{ $item->area_code }}</td>
                                <td>{{ ucfirst(strtolower($item->area_type)) }}</td>
                                <td>{{ $item->postalCode?->postal_code ?? '—' }}</td>
                                <td>
                                    @if ($item->delivery_available === 707)
                                        <span class="badge bg-success">Available</span>
                                    @else
                                        <span class="badge bg-secondary">Unavailable</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status === 707)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $item->sort_order }}</td>
                                <td>
                                    @can('areas.edit')
                                        <a href="{{ route('admin.areas.edit', $item) }}" class="btn btn-icon-only purple">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('areas.delete')
                                        <form action="{{ route('admin.areas.destroy', $item) }}" method="POST" class="d-inline" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon-only red" onclick="return confirm('Are you sure you want to delete this area?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    No areas found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                    </div>
                </div>
                {{ $areas->links() }}
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

@include('admin.partials.location-select')

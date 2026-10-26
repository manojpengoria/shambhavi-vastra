@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Cities</h1>
            <small>Manage cities, towns and villages.</small>
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
            <a href="{{ route('admin.cities.index') }}">Cities</a>
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
                        <i class="fa fa-map-marker"></i>City List
                    </div>
                    @can('cities.create')
                    <div class="actions">
                        <a href="{{ route('admin.cities.create') }}" class="btn btn-warning btn-md pointer">
                        <i class="fa fa-plus"></i> Add New City</a>
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
                    <form method="GET" action="{{ route('admin.cities.index') }}" class="margin-bottom-20">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search name or code">
                            </div>
                            <div class="col-md-3">
                                <select name="state_id" class="form-control js-location-select" data-lookup-url="{{ route('admin.locations.lookup.states') }}" data-placeholder="All States" data-include-disabled="1">
                                    <option value=""></option>
                                    @if($selectedState)
                                        <option value="{{ $selectedState->id }}" selected>{{ $selectedState->selectLabel() }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="city_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="CITY" @selected(request('city_type') === 'CITY')>City</option>
                                    <option value="TOWN" @selected(request('city_type') === 'TOWN')>Town</option>
                                    <option value="VILLAGE" @selected(request('city_type') === 'VILLAGE')>Village</option>
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
                                <a href="{{ route('admin.cities.index') }}" class="btn default">Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-scrollable">
                    <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>State</th>
                        <th>City Name</th>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Postal Codes</th>
                        <th>Areas</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                        <tbody>
                        @forelse($cities as $item)
                            <tr class="odd gradeX">
                                <td>{{ $cities->firstItem() + $loop->index }}</td>
                                <td>{{ $item->state->state_name }}</td>
                                <td>{{ $item->city_name }}</td>
                                <td>{{ $item->city_code }}</td>
                                <td>{{ ucfirst(strtolower($item->city_type)) }}</td>
                                <td>{{ $item->postal_codes_count }}</td>
                                <td>{{ $item->areas_count }}</td>
                                <td>
                                    @if ($item->status === 707)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $item->sort_order }}</td>
                                <td>
                                    @can('cities.edit')
                                        <a href="{{ route('admin.cities.edit', $item) }}" class="btn btn-icon-only purple">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('cities.delete')
                                        <form action="{{ route('admin.cities.destroy', $item) }}" method="POST" class="d-inline" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon-only red" onclick="return confirm('Are you sure you want to delete this city?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    No cities found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                    </div>
                </div>
                {{ $cities->links() }}
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

@include('admin.partials.location-select')

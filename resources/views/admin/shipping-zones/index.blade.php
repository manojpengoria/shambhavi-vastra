@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Shipping Zones</h1>
            <small>Group states and cities into delivery zones.</small>
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
            <a href="{{ route('admin.shipping-zones.index') }}">Shipping Zones</a>
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
                        <i class="fa fa-truck"></i>Shipping Zone List
                    </div>
                    @can('shipping-zones.create')
                    <div class="actions">
                        <a href="{{ route('admin.shipping-zones.create') }}" class="btn btn-warning btn-md pointer">
                        <i class="fa fa-plus"></i> Add New Shipping Zone</a>
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
                    <form method="GET" action="{{ route('admin.shipping-zones.index') }}" class="margin-bottom-20">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search name or code">
                            </div>
                            <div class="col-md-2">
                                <select name="coverage_type" class="form-control">
                                    <option value="">All Coverage</option>
                                    @foreach(\App\Models\ShippingZone::COVERAGE_TYPES as $value => $label)
                                        <option value="{{ $value }}" @selected(request('coverage_type') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
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
                                <a href="{{ route('admin.shipping-zones.index') }}" class="btn default">Reset</a>
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
                        <th>Coverage</th>
                        <th>Covers</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                        <tbody>
                        @forelse($shippingZones as $item)
                            <tr class="odd gradeX">
                                <td>{{ $shippingZones->firstItem() + $loop->index }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->code }}</td>
                                <td>{{ \App\Models\ShippingZone::COVERAGE_TYPES[$item->coverage_type] ?? $item->coverage_type }}</td>
                                <td>
                                    @if ($item->coverage_type === \App\Models\ShippingZone::COVERAGE_STATE)
                                        {{ $item->states_count }} {{ Str::plural('state', $item->states_count) }}
                                    @elseif ($item->coverage_type === \App\Models\ShippingZone::COVERAGE_CITY)
                                        {{ $item->cities_count }} {{ Str::plural('city', $item->cities_count) }}
                                    @else
                                        Rest of India
                                    @endif
                                </td>
                                <td>{{ $item->priority }}</td>
                                <td>
                                    @if ($item->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @can('shipping-zones.edit')
                                        <a href="{{ route('admin.shipping-zones.edit', $item) }}" class="btn btn-icon-only purple">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('shipping-zones.delete')
                                        <form action="{{ route('admin.shipping-zones.destroy', $item) }}" method="POST" class="d-inline" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon-only red" onclick="return confirm('Are you sure you want to delete this shipping zone?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No shipping zones found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                    </div>
                </div>
                {{ $shippingZones->links() }}
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

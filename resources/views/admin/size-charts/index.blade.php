@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <div class="page-head">
        <div class="page-title">
            <h1>Size Charts</h1>
            <small>Manage size charts shown on product pages.</small>
        </div>
    </div>
    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ route('admin.size-charts.index') }}">Size Charts</a>
        </li>
    </ul>
    <!-- END PAGE HEADER-->
    <!-- BEGIN PAGE CONTENT-->
    <div class="row">
        <div class="col-md-12">
            <div class="portlet box blue-madison">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-table"></i>Size Chart List
                    </div>
                    @can('size-charts.create')
                    <div class="actions">
                        <a href="{{ route('admin.size-charts.create') }}" class="btn btn-warning btn-md pointer">
                        <i class="fa fa-plus"></i> Add New Size Chart</a>
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
                    <form method="GET" action="{{ route('admin.size-charts.index') }}" class="margin-bottom-20">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search name">
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="707" @selected(request('status') === '707')>Active</option>
                                    <option value="505" @selected(request('status') === '505')>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn blue"><i class="fa fa-filter"></i> Filter</button>
                                <a href="{{ route('admin.size-charts.index') }}" class="btn default">Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-scrollable">
                    <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Sizes</th>
                        <th>Measurements</th>
                        <th>Unit</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                        <tbody>
                        @forelse($sizeCharts as $item)
                            <tr class="odd gradeX">
                                <td>{{ $sizeCharts->firstItem() + $loop->index }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ collect($item->rows)->pluck(0)->implode(', ') }}</td>
                                <td>{{ collect($item->columns)->slice(1)->implode(', ') }}</td>
                                <td>{{ \App\Models\SizeChart::UNITS[$item->unit] ?? $item->unit }}</td>
                                <td>{{ $item->products_count }}</td>
                                <td>
                                    @if ($item->status === \App\Models\SizeChart::STATUS_ACTIVE)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $item->sort_order }}</td>
                                <td>
                                    @can('size-charts.edit')
                                        <a href="{{ route('admin.size-charts.edit', $item) }}" class="btn btn-icon-only purple">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('size-charts.delete')
                                        <form action="{{ route('admin.size-charts.destroy', $item) }}" method="POST" class="d-inline" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon-only red" onclick="return confirm('Are you sure you want to delete this size chart?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    No size charts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                    </div>
                </div>
                {{ $sizeCharts->links() }}
            </div>
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

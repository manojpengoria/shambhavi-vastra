@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Attribute Values</h1>
            <small>Manage product attribute values.</small>
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
            <a href="{{ route('admin.variant-values.index') }}">Attribute Value</a>
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
                        <i class="fa fa-cogs"></i>Attribute Value List
                    </div>
                    <!-- @can('categories.create') -->
                    <div class="actions">
                        <a href="{{ route('admin.variant-values.create') }}" class="btn btn-warning btn-md pointer">
                        <i class="fa fa-plus"></i> Add New Attribute Value</a>
                    </div>
                    <!-- @endcan -->
                </div>
                <div class="portlet-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <table class="table table-striped table-bordered" id="sample_1">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Attribute</th>
                        <th>Attribute Value</th>
                        <th>Slug</th>
                        <th>Color</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                        <tbody>
                        @forelse($variantValues as $item)
                            <tr class="odd gradeX">
                                <td>{{ $variantValues->firstItem() + $loop->index }}</td>
                                <td>{{ $item->variantType->variant_type_name }}</td>
                                <td>{{ $item->variant_value_name }}</td>
                                <td>{{ $item->slug }}</td>
                                <td>
                                    @if($item->color_code)
                                        <span style="
                                            display:inline-block;
                                            width:22px;
                                            height:22px;
                                            background:{{ $item->color_code }};
                                            border:1px solid #ccc;
                                            vertical-align:middle;
                                        "></span>
                                        {{ $item->color_code }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status === 707)
                                        <span class="badge bg-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $item->sort_order }}</td>
                                <td>
                                    <!-- @can('categories.edit') -->
                                        <a href="{{ route('admin.variant-values.edit', $item) }}"
                                        class="btn btn-icon-only purple">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    <!-- @endcan
                                    @can('categories.delete') -->
                                        <form action="{{ route('admin.variant-values.destroy', $item) }}" method="POST" class="d-inline" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon-only red" onclick="return confirm('Are you sure you want to delete this attribute value?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    <!-- @endcan -->
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No attribute values found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                </div>
                {{ $variantValues->links() }}
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

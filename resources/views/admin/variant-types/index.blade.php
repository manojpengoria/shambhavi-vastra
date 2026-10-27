@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Attributes</h1>
            <small>Manage product attributes.</small>
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
            <a href="{{ route('admin.variant-type.index') }}">Attribute</a>
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
                        <i class="fa fa-cogs"></i>Attribute List
                    </div>
                    <!-- @can('categories.create') -->
                    <div class="actions">
                        <a href="{{ route('admin.variant-type.create') }}" class="btn btn-warning btn-md pointer">
                        <i class="fa fa-plus"></i> Add New Attribute</a>
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
                        <th>Slug</th>
                        <th>Values</th>
                        <th>Used for variants</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                        <tbody>
                        @forelse ($variantTypes as $type)
                            <tr class="odd gradeX">
                                <td>{{ $variantTypes->firstItem() + $loop->index }}</td>
                                <td><strong>{{ $type->variant_type_name }}</strong></td>
                                <td>{{ $type->slug }}</td>
                                <td>
                                    <a href="{{ route('admin.variant-values.index', ['variant_type_id' => $type->id]) }}">
                                        {{ $type->values_count }} Values
                                    </a>
                                </td>
                                <td>
                                    @if ($type->used_for_variants)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($type->status === 707)
                                        <span class="badge bg-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $type->sort_order }}</td>
                                <td>
                                    @can('categories.edit')
                                        <a href="{{ route('admin.variant-type.edit', $type) }}"
                                        class="btn btn-icon-only purple">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('categories.delete')
                                        <form action="{{ route('admin.variant-type.destroy', $type) }}" method="POST" class="d-inline" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon-only red" onclick="return confirm('Are you sure you want to delete this attribute?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No attributes found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                </div>
                {{ $variantTypes->links() }}
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

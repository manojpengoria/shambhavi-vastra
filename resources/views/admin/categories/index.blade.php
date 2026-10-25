@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Categories</h1>
            <small>Manage main categories and subcategories.</small>
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
            <a href="{{ route('admin.categories.index') }}">Categories</a>
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
                        <i class="fa fa-cogs"></i>Category List
                    </div>
                    @can('categories.create')
                    <div class="actions">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-warning btn-md pointer">
                        <i class="fa fa-plus"></i> Add New Category</a>
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
                        <div class="alert alert-danger alert-dismissible">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <table class="table table-striped table-bordered" id="sample_1">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Category Name</th>
                        <th>Parent Category</th>
                        <th>SEO URL</th>
                        <th>Status</th>
                        <th>Sort Order</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                        <tbody>
                        @forelse ($categories as $category)
                            <tr class="odd gradeX">
                                <td>{{ $loop->iteration }}</td>
                            <td>
                                @if ($category->thumb_image)
                                    <img
                                        src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($category->thumb_image) }}"
                                        alt="{{ $category->name }}"
                                        width="55"
                                        height="55"
                                        style="object-fit:cover;border-radius:5px;"
                                    >
                                @else
                                    <span class="text-muted">No image</span>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $category->category_name }}</strong>
                                @if ($category->children_count > 0)
                                    <div>
                                        <small class="text-muted">
                                            {{ $category->children_count }}
                                            subcategories
                                        </small>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($category->parent)
                                    {{ $category->parent->category_name }}
                                @else
                                    <span class="badge bg-info text-dark">
                                        Main Category
                                    </span>
                                @endif
                            </td>
                            <td><code>{{ $category->seo_url }}</code></td>
                            <td>
                                @if ($category->status === 707)
                                    <span class="badge bg-success">
                                        Active
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                @can('categories.edit')
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="btn btn-icon-only purple">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                @endcan
                                @can('categories.delete')
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="d-inline" style="display: inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-icon-only red" onclick="return confirm('Are you sure you want to delete this category?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                No categories found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                    </table>
                </div>
                {{ $categories->links() }}
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

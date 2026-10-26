@extends('admin.layouts.app')

@section('title', 'Products | Admin')

@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Products</h1>
            <small>Manage products, variants and their visibility.</small>
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
            <a href="{{ route('admin.products.index') }}">Products</a>
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
                        <i class="fa fa-cubes"></i>Product List
                    </div>
                    @can('products.create')
                    <div class="actions">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-warning btn-md pointer">
                        <i class="fa fa-plus"></i> Add New Product</a>
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
                    <form method="GET" action="{{ route('admin.products.index') }}" class="margin-bottom-20">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search name or SKU">
                            </div>
                            <div class="col-md-3">
                                <select name="category_id" class="form-control">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(request('category_id') === (string) $category->id)>{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="published" @selected(request('status') === 'published')>Published</option>
                                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                                    <option value="hidden" @selected(request('status') === 'hidden')>Hidden</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn blue"><i class="fa fa-filter"></i> Filter</button>
                                <a href="{{ route('admin.products.index') }}" class="btn default">Reset</a>
                            </div>
                        </div>
                    </form>
                    <div class="table-scrollable">
                    <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Categories</th>
                        <th>Net Price</th>
                        <th>Stock / Variants</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                        <tbody>
                        @forelse($products as $item)
                            <tr class="odd gradeX">
                                <td>{{ $products->firstItem() + $loop->index }}</td>
                                <td>
                                    @if ($item->mainImage)
                                        <img src="{{ $item->mainImage->url() }}" alt="{{ $item->product_name }}" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $item->product_name }}
                                    @if ($item->label)
                                        <br><small class="text-muted">{{ $item->label === 'custom' ? $item->custom_label : \App\Models\Product::LABELS[$item->label] }}</small>
                                    @endif
                                </td>
                                <td>{{ $item->sku }}</td>
                                <td>{{ $item->categories->pluck('category_name')->implode(', ') ?: '—' }}</td>
                                <td>
                                    ₹{{ $item->net_price }}
                                    @if ((float) $item->discount > 0)
                                        <br><small class="text-muted"><s>₹{{ $item->selling_price }}</s></small>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->has_variants)
                                        {{ $item->variants_count }} {{ Str::plural('variant', $item->variants_count) }}
                                    @elseif ($item->track_inventory)
                                        {{ $item->stock_quantity ?? 0 }} in stock
                                    @else
                                        <span class="text-muted">Not tracked</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->is_draft)
                                        <span class="badge bg-secondary">Draft</span>
                                    @elseif ($item->status === \App\Models\Product::STATUS_VISIBLE)
                                        <span class="badge bg-success">Published</span>
                                    @else
                                        <span class="badge bg-warning">Hidden</span>
                                    @endif
                                    @if ($item->publish_at && $item->publish_at->isFuture())
                                        <br><small class="text-muted">From {{ $item->publish_at->format('d M Y, h:i A') }}</small>
                                    @endif
                                </td>
                                <td>
                                    @can('products.edit')
                                        <a href="{{ route('admin.products.edit', $item) }}" class="btn btn-icon-only purple">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    @endcan
                                    @can('products.delete')
                                        <form action="{{ route('admin.products.destroy', $item) }}" method="POST" class="d-inline" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon-only red" onclick="return confirm('Are you sure you want to delete this product with all its images and variants?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    </table>
                    </div>
                </div>
                {{ $products->links() }}
            </div>
            <!-- END EXAMPLE TABLE PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

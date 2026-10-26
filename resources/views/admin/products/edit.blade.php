@extends('admin.layouts.app')

@section('title', 'Edit Product | Admin')

@section('content')
<div class="page-content">
    <div class="page-head">
        <div class="page-title">
            <h1>Edit Product <small>Update product details, images, variants and shipping.</small></h1>
        </div>
    </div>

    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ route('admin.products.index') }}">Products</a>
            <i class="fa fa-circle"></i>
        </li>
        <li><span>Edit</span></li>
    </ul>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.products._form', ['product' => $product])
    </form>
</div>
@endsection

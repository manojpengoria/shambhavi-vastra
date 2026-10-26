@extends('admin.layouts.app')

@section('title', 'Add New Product | Admin')

@section('content')
<div class="page-content">
    <div class="page-head">
        <div class="page-title">
            <h1>Add New Product <small>Create a simple product or one with variants.</small></h1>
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
        <li><span>Create</span></li>
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

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.products._form', ['product' => null])
    </form>
</div>
@endsection

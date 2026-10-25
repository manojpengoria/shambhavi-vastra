@extends('admin.layouts.app')

@section('content')
<div class="page-content">
    <div class="page-head">
        <div class="page-title">
            <h1>Edit Category <small>Update category details and search engine information.</small></h1>
        </div>
    </div>

    <ul class="page-breadcrumb breadcrumb">
        <li>
            <a href="{{ route('admin.dashboard') }}">Home</a>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ route('admin.categories.index') }}">Categories</a>
            <i class="fa fa-circle"></i>
        </li>
        <li><span>Edit Category</span></li>
    </ul>

    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-equalizer font-blue-hoki"></i>
                        <span class="caption-subject font-blue-hoki bold uppercase">Edit Category</span>
                    </div>
                    <div class="actions">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-circle btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="portlet-body form">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form
                        action="{{ route('admin.categories.update', $category) }}"
                        method="POST"
                        enctype="multipart/form-data"
                    >
                        @csrf
                        @method('PUT')

                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="parent_category">Parent Category</label>
                                        <select
                                            id="parent_category"
                                            name="parent_id"
                                            class="form-control select2me"
                                            data-placeholder="No Parent"
                                            style="width: 100%;"
                                        >
                                            <option value="">No Parent</option>
                                            @foreach ($categoryOptions as $option)
                                                <option
                                                    value="{{ $option->id }}"
                                                    @selected((string) old('parent_id', $category->parent_id) === (string) $option->id)
                                                >
                                                    {{ $option->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="category_name">
                                            Category Name <span class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="category_name"
                                            name="category_name"
                                            class="form-control"
                                            value="{{ old('category_name', $category->category_name) }}"
                                            data-original-name="{{ $category->category_name }}"
                                            maxlength="150"
                                            required
                                        >
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="summary">Summary</label>
                                        <textarea
                                            id="summary"
                                            name="summary"
                                            class="form-control"
                                            maxlength="5000"
                                            rows="3"
                                        >{{ old('summary', $category->summary) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="status">Status</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="707" @selected((string) old('status', $category->status) === '707')>Active</option>
                                            <option value="505" @selected((string) old('status', $category->status) === '505')>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="sort_order">Sort Order</label>
                                        <input
                                            type="number"
                                            id="sort_order"
                                            name="sort_order"
                                            class="form-control"
                                            value="{{ old('sort_order', $category->sort_order) }}"
                                            min="0"
                                            required
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="thumb_image">Thumbnail Image</label>
                                        <input
                                            type="file"
                                            id="thumb_image"
                                            name="thumb_image"
                                            class="form-control"
                                            accept=".jpg,.jpeg,.png,.webp"
                                        >
                                        <small class="help-block">Optional. JPG, PNG or WebP, up to 2 MB.</small>
                                        @if ($category->thumb_image)
                                            <div class="current-category-image">
                                                <span>Current thumbnail:</span>
                                                <img
                                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($category->thumb_image) }}"
                                                    alt="{{ $category->category_name }} thumbnail"
                                                    width="100"
                                                    height="100"
                                                    style="object-fit: cover;"
                                                >
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label" for="large_image">Large Image</label>
                                        <input
                                            type="file"
                                            id="large_image"
                                            name="large_image"
                                            class="form-control"
                                            accept=".jpg,.jpeg,.png,.webp"
                                        >
                                        <small class="help-block">Optional. JPG, PNG or WebP, up to 5 MB.</small>
                                        @if ($category->large_image)
                                            <div class="current-category-image">
                                                <span>Current large image:</span>
                                                <img
                                                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($category->large_image) }}"
                                                    alt="{{ $category->category_name }} large image"
                                                    width="140"
                                                    height="100"
                                                    style="object-fit: cover;"
                                                >
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div> -->

                            <h3 class="form-section">Search Engines</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="meta_title">
                                            Meta Title <small><em>(Recommended length 70 characters)</em></small>
                                        </label>
                                        <input
                                            type="text"
                                            id="meta_title"
                                            name="meta_title"
                                            class="form-control"
                                            value="{{ old('meta_title', $category->meta_title) }}"
                                            data-generated-default="{{ $category->category_name }}"
                                            maxlength="255"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="meta_keywords">
                                            Meta Keywords <small><em>(Recommended length 160 characters)</em></small>
                                        </label>
                                        <input
                                            type="text"
                                            id="meta_keywords"
                                            name="meta_keywords"
                                            class="form-control"
                                            value="{{ old('meta_keywords', $category->meta_keywords) }}"
                                            data-generated-default="{{ $category->category_name }}"
                                            maxlength="2000"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="meta_description">
                                            Meta Description <small><em>(Recommended length 250 characters)</em></small>
                                        </label>
                                        <textarea
                                            id="meta_description"
                                            name="meta_description"
                                            class="form-control"
                                            data-generated-default="{{ $category->category_name }}"
                                            maxlength="5000"
                                            rows="3"
                                        >{{ old('meta_description', $category->meta_description) }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label" for="seo_url">SEO URL</label>
                                        <div class="input-group">
                                            <span class="input-group-addon">https://shambhavivastra.com/categories/</span>
                                            <input
                                                type="text"
                                                id="seo_url"
                                                name="seo_url"
                                                class="form-control"
                                                value="{{ old('seo_url', $category->seo_url) }}"
                                                data-generated-default="{{ $category->category_name }}"
                                                maxlength="200"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions right">
                            <a href="{{ route('admin.categories.index') }}" class="btn default">Cancel</a>
                            <button type="submit" class="btn green">Update Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <style>
        #parent_category + .select2-container--bootstrap .select2-selection--single {
            height: 34px;
        }

        .current-category-image {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 10px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoryName = document.getElementById('category_name');
            const seoUrl = document.getElementById('seo_url');
            const seoFields = [
                document.getElementById('meta_title'),
                document.getElementById('meta_keywords'),
                document.getElementById('meta_description'),
                seoUrl,
            ];
            const slugify = function (value) {
                return value
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            };
            const generatedDefaults = seoFields.map(function (field) {
                return field === seoUrl
                    ? slugify(field.dataset.generatedDefault)
                    : field.dataset.generatedDefault;
            });
            const manuallyEditedFields = new Set(
                seoFields.filter(function (field, index) {
                    return field.value.trim() !== ''
                        && field.value !== generatedDefaults[index];
                })
            );

            seoFields.forEach(function (field) {
                field.addEventListener('input', function () {
                    manuallyEditedFields.add(field);
                });
            });

            categoryName.addEventListener('blur', function () {
                const value = categoryName.value.trim();

                if (value === '') {
                    return;
                }

                const generatedValues = [
                    value,
                    value,
                    value,
                    slugify(value),
                ];

                seoFields.forEach(function (field, index) {
                    if (!manuallyEditedFields.has(field)) {
                        field.value = generatedValues[index];
                    }
                });
            });
        });
    </script>
@endpush
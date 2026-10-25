@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Create Category <small>Add a main category or subcategory.</small></h1>
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
            <a href="javascript;">Category</a>
            <i class="fa fa-circle"></i>
        </li>
    </ul>
    <!-- END PAGE BREADCRUMB -->
    <!-- END PAGE HEADER-->
    <!-- BEGIN PAGE CONTENT-->
    <div class="row">
        <div class="col-md-12 ">
            <!-- BEGIN SAMPLE FORM PORTLET-->
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="icon-equalizer font-blue-hoki"></i>
                        <span class="caption-subject font-blue-hoki bold uppercase">Create Category</span>
                    </div>
                    <div class="actions">
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-circle btn-default btn-sm" data-toggle="tooltip" title="Back to Users List">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                        <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="" title="">
                        </a>
					</div>
                </div>
                <div class="portlet-body form">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.categories.store') }}" class="" role="form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Parent Category<span class="text-danger">*</span></label>
                                        <select id="parent_category" name="parent_id" class="form-control input-large select2me" data-placeholder="No Parent" style="width: 100%;">
                                            <option value="">No Parent</option>
                                            @foreach ($categoryOptions as $category)
                                                <option value="{{ $category->id }}"
                                                    @selected(old('parent_id') == $category->id)>
                                                    {{ $category->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
								</div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Category Name<span class="text-danger">*</span></label> <small>(e.g.: Men, Women, Jewelery etc)</small>
                                        <input type="text" id="category_name" name="category_name" class="form-control" value="{{ old('category_name') }}" placeholder="e.g. Men, Women, Sarees" maxlength="255" required>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Summary</label>
                                        <textarea id="summary" name="summary" class="form-control" maxlength="5000" rows="3">{{ old('summary') }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Status</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="707" {{ old('status', '707') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="505" {{ old('status') == '505' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Sort Order</label>
                                        <input type="text" id="sort_order" name="sort_order" class="form-control" value="0" required>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <h3 class="form-section">Search Engines</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Meta Title <small><em>(Recommended length 70 characters)</em></small></label>
                                        <input type="text" id="meta_title" name="meta_title" class="form-control" value="{{ old('meta_title') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Meta Keyword <small><em>(Recommended length 160 characters)</em></small></label>
                                        <input type="text" id="meta_keywords" name="meta_keywords" class="form-control" value="{{ old('meta_keywords') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Meta Description <small><em>(Recommended length 250 characters)</em></small></label>
                                        <input type="text" id="meta_description" name="meta_description" class="form-control" value="{{ old('meta_description') }}">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Seo Url</label>
                                        <div class="input-group">
                                            <span class="input-group-addon" style="background-color: #f4f4f4">https://shambhavivastra.com/categories/</span>
                                            <input type="text" class="form-control" name="seo_url" id="seo_url" maxlength="50" value="{{ old('seo_url') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/row-->
                        </div>
                        <div class="form-actions right">
                            <button type="button" class="btn default">Cancel</button>
                            <button type="submit" class="btn green">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END SAMPLE FORM PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

@push('styles')
    <style>
        #parent_category + .select2-container--bootstrap .select2-selection--single {
            height: 34px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoryName = document.getElementById('category_name');
            const seoFields = [
                document.getElementById('meta_title'),
                document.getElementById('meta_keywords'),
                document.getElementById('meta_description'),
                document.getElementById('seo_url'),
            ];
            const manuallyEditedFields = new Set(
                seoFields.filter((field) => field.value.trim() !== '')
            );

            seoFields.forEach((field) => {
                field.addEventListener('input', function () {
                    manuallyEditedFields.add(field);
                });
            });

            categoryName.addEventListener('blur', function () {
                const value = categoryName.value.trim();

                if (value === '') {
                    return;
                }

                const seoUrl = value
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');

                const generatedValues = [
                    value,
                    value,
                    value,
                    seoUrl,
                ];

                seoFields.forEach((field, index) => {
                    if (!manuallyEditedFields.has(field)) {
                        field.value = generatedValues[index];
                    }
                });
            });
        });
    </script>
@endpush
@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Edit Variant Type <small>Edit a main variant type.</small></h1>
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
            <a href="javascript;">Variant Type</a>
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
                        <span class="caption-subject font-blue-hoki bold uppercase">Edit Variant Type</span>
                    </div>
                    <div class="actions">
                        <a href="{{ route('admin.variant-type.index') }}" class="btn btn-circle btn-default btn-sm" data-toggle="tooltip" title="Back to Users List">
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
                    <form action="{{ route('admin.variant-type.update', $variantType) }}" class="" role="form" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Variant Type Name<span class="text-danger">*</span></label> <small>(e.g.: Men, Women, Jewelery etc)</small>
                                        <input type="text" id="variant-name" name="variant_type_name" class="form-control" value="{{ old('variant_type_name', $variantType->variant_type_name) }}" data-original-name="{{ $variantType->variant_type_name }}" placeholder="e.g. Size, Color, Fabric" maxlength="255" required>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Slug</label>
                                        <input type="text" id="variant-slug" name="slug" class="form-control" value="{{ old('slug', $variantType->slug) }}" placeholder="Automatically generated" maxlength="255" readonly>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Description</label>
                                        <textarea id="description" name="description" class="form-control" maxlength="5000" rows="3">{{ old('description', $variantType->description) }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Status</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="707" @selected((string) old('status', $variantType->status) === '707')>Active</option>
                                            <option value="505" @selected((string) old('status', $variantType->status) === '505')>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Sort Order</label>
                                        <input type="text" id="sort_order" name="sort_order" class="form-control" value="{{ old('sort_order', $variantType->sort_order) }}" required>
                                    </div>
                                </div>
                                <!--/span-->
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
            const variantName = document.getElementById('variant-name');
            const seoFields = [
                document.getElementById('variant-slug'),
            ];
            const manuallyEditedFields = new Set(
                seoFields.filter((field) => field.value.trim() !== '')
            );

            seoFields.forEach((field) => {
                field.addEventListener('input', function () {
                    manuallyEditedFields.add(field);
                });
            });

            variantName.addEventListener('blur', function () {
                const value = variantName.value.trim();

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
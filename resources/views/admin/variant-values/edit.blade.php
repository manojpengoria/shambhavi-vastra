@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Edit Variant Value <small>Edit a main variant value.</small></h1>
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
            <a href="javascript;">Variant Value</a>
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
                        <span class="caption-subject font-blue-hoki bold uppercase">Edit Variant Value</span>
                    </div>
                    <div class="actions">
                        <a href="{{ route('admin.variant-values.index') }}" class="btn btn-circle btn-default btn-sm" data-toggle="tooltip" title="Back to Variant Value List">
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
                    <form action="{{ route('admin.variant-values.update', $variantValue) }}" class="" role="form" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Variant Type <span class="text-danger">*</span></label>
                                        <select name="variant_type_id" id="variant_type_id" class="form-control" required>
                                            <option value="">Select Variant Type</option>
                                            @foreach($variantTypes as $type)
                                                <option value="{{ $type->id }}"
                                                    @selected(old('variant_type_id', $variantValue->variant_type_id) == $type->id)>
                                                    {{ $type->variant_type_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Variant Value Name<span class="text-danger">*</span></label> <small>(e.g.: S, M, XL, Red, Cotton etc)</small>
                                        <input type="text" id="variant-value" name="variant_value_name" class="form-control" value="{{ old('variant_value_name', $variantValue->variant_value_name) }}" placeholder="e.g. S, M, XL, Red, Cotton" maxlength="100" required>
                                    </div>
                                </div>
                                <!--/span-->
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Slug</label>
                                        <input type="text" id="variant-slug" name="slug" class="form-control" value="{{ old('slug', $variantValue->slug) }}" placeholder="Automatically generated" maxlength="255" readonly>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <label class="control-label">Color Code</label>
                                    <input type="text" class="colorpicker-default form-control" value="{{ old('color_code', $variantValue->color_code) }}">
                                    <small class="text-muted">
                                        Optional. Use for Color variant values.
                                    </small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Status</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="707" @selected((string) old('status', $variantValue->status) === '707')>Active</option>
                                            <option value="505" @selected((string) old('status', $variantValue->status) === '505')>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <!--/span-->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">Sort Order</label>
                                        <input type="text" id="sort_order" name="sort_order" class="form-control" value="{{ old('sort_order', $variantValue->sort_order) }}" required>
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
            const variantValue = document.getElementById('variant-value');
            const colorPicker = document.getElementById('color-picker');
            const colorCode = document.getElementById('color-code');
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

            variantValue.addEventListener('blur', function () {
                const value = variantValue.value.trim();

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

            // colorPicker.addEventListener('input', function () {
            //     colorCode.value = this.value.toUpperCase();
            // });

            // colorCode.addEventListener('input', function () {
            //     if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            //         colorPicker.value = this.value;
            //     }
            // });
        });
    </script>
@endpush
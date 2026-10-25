@extends('admin.layouts.app')
@section('content')
<div class="page-content">
    <!-- BEGIN PAGE HEADER-->
    <!-- BEGIN PAGE HEAD -->
    <div class="page-head">
        <!-- BEGIN PAGE TITLE -->
        <div class="page-title">
            <h1>Shipping Settings <small>Store-wide shipping, COD and return rules.</small></h1>
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
            <span>Shipping</span>
            <i class="fa fa-circle"></i>
        </li>
        <li>
            <a href="{{ route('admin.shipping-settings.edit') }}">Shipping Settings</a>
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
                        <i class="icon-settings font-blue-hoki"></i>
                        <span class="caption-subject font-blue-hoki bold uppercase">Shipping Settings</span>
                    </div>
                </div>
                <div class="portlet-body form">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            {{ session('success') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.shipping-settings.update') }}" role="form" method="POST">
                        @csrf
                        @method('PUT')
                        <fieldset @disabled(! auth('admin')->user()->can('shipping-settings.edit'))>
                        <div class="form-body">
                            <h4 class="form-section">Defaults</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Default Warehouse</label>
                                        <select name="default_warehouse_id" class="form-control">
                                            <option value="">None</option>
                                            @foreach($warehouses as $warehouse)
                                                <option value="{{ $warehouse->id }}" @selected((string) old('default_warehouse_id', $shippingSetting->default_warehouse_id) === (string) $warehouse->id)>{{ $warehouse->name }} ({{ $warehouse->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Default Courier Partner</label>
                                        <select name="default_courier_partner_id" class="form-control">
                                            <option value="">None</option>
                                            @foreach($courierPartners as $courierPartner)
                                                <option value="{{ $courierPartner->id }}" @selected((string) old('default_courier_partner_id', $shippingSetting->default_courier_partner_id) === (string) $courierPartner->id)>{{ $courierPartner->name }} ({{ $courierPartner->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Volumetric Divisor <span class="text-danger">*</span></label> <small>(L × B × H ÷ divisor)</small>
                                        <input type="number" name="volumetric_divisor" class="form-control" value="{{ old('volumetric_divisor', $shippingSetting->volumetric_divisor ?? 5000) }}" min="1" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Free Shipping Above (₹)</label> <small>(blank = never free)</small>
                                        <input type="number" name="free_shipping_above" class="form-control" value="{{ old('free_shipping_above', $shippingSetting->free_shipping_above) }}" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Show Expected Delivery Date</label>
                                        <select name="show_delivery_date" class="form-control" required>
                                            <option value="1" @selected((string) old('show_delivery_date', (int) $shippingSetting->show_delivery_date) === '1')>Yes</option>
                                            <option value="0" @selected((string) old('show_delivery_date', (int) $shippingSetting->show_delivery_date) === '0')>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Pincode Check Before Add to Cart</label>
                                        <select name="pincode_check_before_cart" class="form-control" required>
                                            <option value="1" @selected((string) old('pincode_check_before_cart', (int) $shippingSetting->pincode_check_before_cart) === '1')>Yes</option>
                                            <option value="0" @selected((string) old('pincode_check_before_cart', (int) $shippingSetting->pincode_check_before_cart) === '0')>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <h4 class="form-section">Cash on Delivery</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">COD Enabled</label>
                                        <select name="cod_enabled" class="form-control" required>
                                            <option value="1" @selected((string) old('cod_enabled', (int) $shippingSetting->cod_enabled) === '1')>Yes</option>
                                            <option value="0" @selected((string) old('cod_enabled', (int) $shippingSetting->cod_enabled) === '0')>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">COD Minimum Order (₹)</label>
                                        <input type="number" name="cod_min_order" class="form-control" value="{{ old('cod_min_order', $shippingSetting->cod_min_order) }}" min="0" step="0.01">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">COD Maximum Order (₹)</label>
                                        <input type="number" name="cod_max_order" class="form-control" value="{{ old('cod_max_order', $shippingSetting->cod_max_order) }}" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>

                            <h4 class="form-section">Returns</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Return Window (days) <span class="text-danger">*</span></label>
                                        <input type="number" name="return_window_days" class="form-control" value="{{ old('return_window_days', $shippingSetting->return_window_days ?? 7) }}" min="0" max="365" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Return Shipping Paid By <span class="text-danger">*</span></label>
                                        <select name="return_shipping_paid_by" class="form-control" required>
                                            @foreach(\App\Models\ShippingSetting::RETURN_PAID_BY as $value => $label)
                                                <option value="{{ $value }}" @selected(old('return_shipping_paid_by', $shippingSetting->return_shipping_paid_by ?? \App\Models\ShippingSetting::PAID_BY_CUSTOMER) === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @can('shipping-settings.edit')
                            <div class="form-actions right">
                                <button type="submit" class="btn green">Save Settings</button>
                            </div>
                        @endcan
                        </fieldset>
                    </form>
                </div>
            </div>
            <!-- END SAMPLE FORM PORTLET-->
        </div>
    </div>
    <!-- END PAGE CONTENT-->
</div>
@endsection

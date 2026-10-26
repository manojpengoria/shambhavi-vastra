@php
    $publishAt = old('publish_at', $product?->publish_at?->format('Y-m-d\TH:i'));
    $isVisible = (string) old('is_visible', $product ? (int) ($product->status === \App\Models\Product::STATUS_VISIBLE) : 1) === '1';
    $visibleAfterLogin = (string) old('visible_after_login', (int) ($product?->visible_after_login ?? false));
    $tracksInventory = (bool) old('track_inventory', $product?->track_inventory ?? false);
    $isTaxable = (bool) old('taxable', $product?->taxable ?? false);
    $hasVariants = (bool) old('has_variants', $product?->has_variants ?? false);
    $generateVariants = (bool) old('generate_variants', $product?->has_variants ?? false);
    $isReturnable = (bool) old('is_returnable', $product?->is_returnable ?? true);
    $customSeoUrl = (bool) old('custom_seo_url', $product !== null);
    $selectedLabel = old('label', $product ? $product->label : 'new');
@endphp
<div id="product-create-form" class="form-row-sepe">
    <div class="row">
        <div class="col-md-8">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-sitemap"></i>
                        <span class="caption-subject bold uppercase">Categories</span>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="form-group mb-0">
                        <label class="control-label" for="select2_sample_modal_5">Categories <span class="text-danger">*</span></label>
                        <select
                            id="select2_sample_modal_5"
                            name="category_ids[]"
                            class="form-control"
                            data-placeholder="Search and select categories..."
                            multiple
                            style="width: 100%;"
                        >
                            @foreach ($categoryOptions as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(in_array((string) $category->id, $selectedCategoryIds, true))
                                >
                                    {{ $category->display_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-eye"></i>
                        <span class="caption-subject bold uppercase">Visibility</span>
                    </div>
                    <div class="actions">
                        <button
                            type="button"
                            id="toggle-publish-schedule"
                            class="btn btn-icon-only btn-default"
                            aria-expanded="{{ $publishAt ? 'true' : 'false' }}"
                            aria-controls="publish-schedule"
                            aria-label="Set publish date and time"
                        >
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="visibility-toggle">
                        <label class="control-label" for="is_visible">Visible on your store</label>
                        <input type="hidden" name="is_visible" value="0">
                        <input
                            type="checkbox"
                            id="is_visible"
                            name="is_visible"
                            value="1"
                            class="make-switch"
                            data-size="small"
                            data-on-color="success"
                            data-on-text="YES"
                            data-off-color="default"
                            data-off-text="NO"
                            @checked($isVisible)
                        >
                    </div>

                    <div
                        id="publish-schedule"
                        class="publish-schedule"
                        @if (! $publishAt) hidden @endif
                    >
                        <label class="control-label" for="publish_at">Set Publish Date &amp; Time:</label>
                        <div class="input-group">
                            <input
                                type="datetime-local"
                                id="publish_at"
                                name="publish_at"
                                class="form-control"
                                value="{{ $publishAt }}"
                            >
                            <span class="input-group-btn">
                                <button
                                    type="button"
                                    id="open-publish-picker"
                                    class="btn btn-default"
                                    aria-label="Open date and time picker"
                                >
                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                </button>
                            </span>
                        </div>
                    </div>

                    <hr>
                    <div class="visibility-scope">
                        <div class="form-group">
                            <div class="md-radio-list">
                                <div class="md-radio">
                                    <input type="radio" id="show_product_condition_1" name="visible_after_login" class="md-radiobtn" value="0" @checked($visibleAfterLogin !== '1')>
                                    <label for="show_product_condition_1">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>
                                    Visible All </label>
                                </div>
                                <div class="md-radio">
                                    <input type="radio" id="show_product_condition_2" name="visible_after_login" class="md-radiobtn" value="1" @checked($visibleAfterLogin === '1')>
                                    <label for="show_product_condition_2">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span>
                                    Visible After Customer Login </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-info-circle"></i>
                <span class="caption-subject bold uppercase">Product Details</span>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <label class="control-label" for="product_name">Product Name / Title <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            id="product_name"
                            name="product_name"
                            class="form-control"
                            value="{{ old('product_name', $product?->product_name) }}"
                            maxlength="255"
                            required
                        >
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="sort_order">Sort Order</label>
                        <input type="number" id="sort_order" name="sort_order" class="form-control" value="{{ old('sort_order', $product?->sort_order ?? 0) }}" min="0">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="short_summary_brand">Short Summary / Brand</label>
                        <input type="text" id="short_summary_brand" name="short_summary_brand" class="form-control" value="{{ old('short_summary_brand', $product?->short_summary_brand) }}" maxlength="255">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label" for="summary">Summary</label>
                <textarea id="summary" name="summary" class="form-control" rows="3" maxlength="2000">{{ old('summary', $product?->summary) }}</textarea>
            </div>

            <div class="form-group">
                <label class="control-label" for="description">Description</label>
                <textarea id="summernote_1" name="description" class="form-control" rows="8">{{ old('description', $product?->description) }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="hsn_sac_code">HSN / SAC Code</label>
                        <input type="text" id="hsn_sac_code" name="hsn_sac_code" class="form-control" value="{{ old('hsn_sac_code', $product?->hsn_sac_code) }}" maxlength="8" placeholder="4 to 8 digits">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="sku">Product Code / SKU <span class="text-danger">*</span></label>
                        <input type="text" id="sku" name="sku" class="form-control" value="{{ old('sku', $product?->sku) }}" maxlength="100" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="barcode">Barcode <small class="caption-helper"><i>(UPC, ISBN, GTIN)</i></small></label>
                        <input type="text" id="barcode" name="barcode" class="form-control" value="{{ old('barcode', $product?->barcode) }}" maxlength="50">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label" for="origin_country">Origin <small class="caption-helper"><i>(e.g. India)</i></small></label>
                        <select id="origin_country" name="origin_country" class="form-control">
                            <option value="">Select Country</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country }}" @selected(old('origin_country', $product?->origin_country) === $country)>{{ $country }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="md-checkbox-list">
                        <div class="md-checkbox">
                            <input type="hidden" name="track_inventory" value="0">
                            <input type="checkbox" id="checkbox30" class="md-check" name="track_inventory" value="1" @checked($tracksInventory)>
                            <label for="checkbox30">
                            <span></span>
                            <span class="check"></span>
                            <span class="box"></span>
                            Track inventory for this product </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label class="control-label" for="stock_quantity">Stock Quantity</label>
                        <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product?->stock_quantity) }}" min="0" @disabled(! $tracksInventory || $hasVariants)>
                        <p class="help-block">For products with variants, enter quantity per variant.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-picture-o"></i>
                <span class="caption-subject bold uppercase">Images</span>
                <span class="caption-helper">Upload up to {{ \App\Models\Product::MAX_IMAGES }} product images (JPG, PNG or WEBP, max 2 MB each)</span>
            </div>
        </div>
        <div class="portlet-body">
            <div class="product-image-grid">
                @for ($imageNumber = 1; $imageNumber <= \App\Models\Product::MAX_IMAGES; $imageNumber++)
                    @php
                        $existingImage = $imagesBySlot->get($imageNumber);
                    @endphp
                    <div class="product-image-grid-item">
                        <div class="product-image-slot" data-image-slot>
                            @if ($existingImage)
                                <input type="hidden" name="existing_image_ids[{{ $imageNumber }}]" value="{{ $existingImage->id }}" class="product-existing-image-input">
                            @endif
                            <label class="product-image-upload" for="product_image_{{ $imageNumber }}">
                                    <img
                                        class="product-image-preview"
                                        alt="Product image {{ $imageNumber }} preview"
                                        @if ($existingImage) src="{{ $existingImage->url() }}" @else hidden @endif
                                    >
                                    <span class="product-image-upload-prompt" @if ($existingImage) hidden @endif>
                                        <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                                        <span>Upload image {{ $imageNumber }}</span>
                                    </span>
                                    <input
                                        type="file"
                                        id="product_image_{{ $imageNumber }}"
                                        name="product_images[{{ $imageNumber }}]"
                                        accept="image/jpeg,image/png,image/webp"
                                        class="product-image-input"
                                    >
                            </label>
                            <button
                                    type="button"
                                    class="product-image-remove"
                                    aria-label="Remove image {{ $imageNumber }}"
                                    title="Remove image"
                                    @unless ($existingImage) hidden @endunless
                            >
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>
                            <label class="product-main-image has-warning" @unless ($existingImage) hidden @endunless>
                                    <input
                                        type="radio"
                                        id="main_product_image_{{ $imageNumber }}"
                                        name="main_product_image"
                                        value="{{ $imageNumber }}"
                                        class="product-main-image-radio md-radiobtn"
                                        aria-label="Set image {{ $imageNumber }} as main image"
                                        @checked($imageNumber === $mainImageSlot)
                                    >
                            </label>
                        </div>
                    </div>
                @endfor
            </div>
            <div style="padding:0; border-bottom: dashed 1px #ddd; margin-bottom: 2em;">&nbsp;</div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Youtube Video Code</label>
                        <div class="input-group">
                            <span class="input-group-addon" style="background-color: #f4f4f4">https://www.youtube.com/embed/</span>
                            <input type="text" class="form-control" name="youtube_video" id="youtube_video" maxlength="11" value="{{ old('youtube_video', $product?->youtube_video) }}" placeholder="e.g. dQw4w9WgXcQ">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-money"></i>
                <span class="caption-subject bold uppercase">Pricing</span>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="selling_price">Selling Price <span class="text-danger">*</span></label>
                        <input type="number" id="selling_price" name="selling_price" class="form-control" value="{{ old('selling_price', $product?->selling_price) }}" min="0" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="discount">Discount on Selling</label>
                        <input type="number" id="discount" name="discount" class="form-control" value="{{ old('discount', $product?->discount) }}" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="net_price">Net Price</label>
                        <input type="number" id="net_price" class="form-control" value="{{ old('net_price', $product?->net_price) }}" min="0" step="0.01" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="moq">MOQ / Quantity <span class="text-danger">*</span></label>
                        <input type="number" id="moq" name="moq" class="form-control" value="{{ old('moq', $product?->moq ?? 1) }}" min="1" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="purchase_cost">Purchase Price / Cost</label>
                        <input type="number" id="cost" name="purchase_cost" class="form-control" value="{{ old('purchase_cost', $product?->purchase_cost) }}" min="0" step="0.01">
                    </div>
                </div>
            </div>

            <div class="md-checkbox-list" style="margin-top: 2%; margin-bottom: 2%;">
                <label class="control-label" for="tax">Taxable Product</label>
                <div class="md-checkbox">
                    <input type="hidden" name="taxable" value="0">
                    <input type="checkbox" id="checkbox31" class="md-check" name="taxable" value="1" @checked($isTaxable)>
                    <label for="checkbox31">
                    <span></span>
                    <span class="check"></span>
                    <span class="box"></span>
                     <small> <i>(If this product is taxable, then tick this checkbox)</i> </small></label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label class="control-label" for="tax_rate">Tax Rate</label>
                        <div class="input-group">
                            <input type="number" id="tax_rate" name="tax_rate" class="form-control" value="{{ old('tax_rate', $product?->tax_rate) }}" min="0" max="100" step="0.01" @disabled(! $isTaxable)>
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-sliders"></i>
                <span class="caption-subject bold uppercase">Variants / Options</span>
            </div>
        </div>
        <div class="portlet-body">
            <div class="md-checkbox-list">
                <div class="md-checkbox">
                    <input type="hidden" name="has_variants" value="0">
                    <input type="checkbox" id="has_variants" class="md-check" name="has_variants" value="1" @checked($hasVariants)>
                    <label for="has_variants">
                    <span></span>
                    <span class="check"></span>
                    <span class="box"></span>
                     Create variant options for this product<small> <i> (e.g. multiple sizes and/or fabrics)</i> </small></label>
                </div>
            </div>

            <div id="variant-options-panel" @unless($hasVariants) hidden @endunless>
                <hr>
                <h4>Variant Options</h4>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr role="row" class="heading">
                            <th width="10%">
                                #
                            </th>
                            <th width="40%">
                                Variant Name
                            </th>
                            <th width="60%">
                                Variant Values
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                    @for ($index = 0; $index < 3; $index++)
                        @php
                            $variantOptionRow = $variantOptionRows[$index] ?? [];
                            $selectedVariantTypeId = (string) ($variantOptionRow['variant_type_id'] ?? '');
                            $selectedVariantValueIds = array_map(
                                'strval',
                                $variantOptionRow['variant_value_ids'] ?? []
                            );
                            $rowEnabled = $hasVariants && !empty($variantOptionRow['enabled']);
                        @endphp
                        <tr class="variant-option-row" data-row-index="{{ $index }}">
                            <td>
                                <div class="md-checkbox-list">
                                    <div class="md-checkbox">
                                        <input type="checkbox"
                                            id="variant-row-enabled-{{ $index }}"
                                            class="md-check variant-row-enabled"
                                            name="variants[{{ $index }}][enabled]"
                                            value="1"
                                            @checked($rowEnabled)
                                            @disabled(! $hasVariants)
                                        >
                                        <label for="variant-row-enabled-{{ $index }}">
                                        <span></span>
                                        <span class="check"></span>
                                        <span class="box"></span></label>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <select
                                    id="variant-type-{{ $index }}"
                                    class="form-control variant-type-select"
                                    name="variants[{{ $index }}][variant_type_id]"
                                    @disabled(! $rowEnabled)
                                >
                                    <option value="">Select variant type</option>
                                    @foreach ($variantTypes as $variantType)
                                        <option
                                            value="{{ $variantType->id }}"
                                            data-values="{{ $variantType->values_json }}"
                                            @selected($selectedVariantTypeId === (string) $variantType->id)
                                        >{{ $variantType->variant_type_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select
                                    id="variant-values-{{ $index }}"
                                    class="form-control variant-values-select"
                                    name="variants[{{ $index }}][variant_value_ids][]"
                                    multiple
                                    data-selected-values="{{ json_encode($selectedVariantValueIds) }}"
                                    @disabled(! $rowEnabled || $selectedVariantTypeId === '')
                                >
                                </select>
                            </td>
                        </tr>
                    @endfor
                    </tbody>
                </table>
                <div class="md-checkbox-list">
                    <div class="md-checkbox">
                        <input type="checkbox"
                            id="generate_variants"
                            class="md-check"
                            name="generate_variants"
                            value="1"
                            @checked($generateVariants)
                            disabled
                        >
                        <label for="generate_variants">
                            <span></span>
                            <span class="check"></span>
                            <span class="box"></span>
                            Generate variants based on the options defined above
                        </label>
                    </div>
                </div>
                <div id="generated-variants-section" class="generated-variants-section" hidden>
                    <h4>Select which variants will be created:</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover generated-variants-table">
                            <thead>
                                <tr class="heading">
                                    <th aria-label="Include variant"></th>
                                    <th>Variant</th>
                                    <th>Product Price</th>
                                    <th>Discount Price</th>
                                    <th>Net Price</th>
                                    <th>SKU (Optional)</th>
                                    <th>Barcode (Optional)</th>
                                    <th>Weight (kg)</th>
                                    <th>Quantity (Optional)</th>
                                    <th>Main</th>
                                </tr>
                            </thead>
                            <tbody
                                id="generated-variants-body"
                                data-initial-variants="{{ json_encode((object) $initialVariants) }}"
                                data-main-variant-key="{{ $mainVariantKey }}"
                            ></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-truck"></i>
                <span class="caption-subject bold uppercase">Shipping</span>
                <span class="caption-helper">Used to calculate shipping charges and delivery estimates</span>
            </div>
        </div>
        <div class="portlet-body">
            <h4 class="form-section">Weight &amp; Dimensions</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="weight">Weight (kg) <span class="text-danger">*</span></label>
                        <input type="number" id="weight" name="weight" class="form-control" value="{{ old('weight', $product?->weight) }}" min="0.001" step="0.001" required>
                        <p class="help-block">Including packaging. Variants can override it.</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="length">Length (cm)</label>
                        <input type="number" id="length" name="length" class="form-control" value="{{ old('length', $product?->length) }}" min="0.01" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="breadth">Breadth (cm)</label>
                        <input type="number" id="breadth" name="breadth" class="form-control" value="{{ old('breadth', $product?->breadth) }}" min="0.01" step="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="height">Height (cm)</label>
                        <input type="number" id="height" name="height" class="form-control" value="{{ old('height', $product?->height) }}" min="0.01" step="0.01">
                    </div>
                </div>
            </div>
            <p class="help-block" id="volumetric-weight-preview" data-volumetric-divisor="{{ $volumetricDivisor }}"></p>

            <h4 class="form-section">Class &amp; Packaging</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="shipping_class_id">Shipping Class</label>
                        <select id="shipping_class_id" name="shipping_class_id" class="form-control">
                            <option value="">No shipping class</option>
                            @foreach ($shippingClasses as $shippingClass)
                                <option value="{{ $shippingClass->id }}" @selected((string) old('shipping_class_id', $product?->shipping_class_id) === (string) $shippingClass->id)>
                                    {{ $shippingClass->name }}{{ (float) $shippingClass->extra_charge > 0 ? ' (+₹'.$shippingClass->extra_charge.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <p class="help-block">Adds the class extra charge to the shipping amount.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" for="shipping_package_id">Package</label>
                        <select id="shipping_package_id" name="shipping_package_id" class="form-control">
                            <option value="">No default package</option>
                            @foreach ($shippingPackages as $shippingPackage)
                                <option
                                    value="{{ $shippingPackage->id }}"
                                    data-length="{{ $shippingPackage->length }}"
                                    data-breadth="{{ $shippingPackage->breadth }}"
                                    data-height="{{ $shippingPackage->height }}"
                                    @selected((string) old('shipping_package_id', $product?->shipping_package_id) === (string) $shippingPackage->id)
                                >
                                    {{ $shippingPackage->name }} ({{ $shippingPackage->length }} × {{ $shippingPackage->breadth }} × {{ $shippingPackage->height }} cm)
                                </option>
                            @endforeach
                        </select>
                        <p class="help-block">Empty dimensions are filled from the selected package.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="checkbox">
                        <input type="hidden" name="free_shipping" value="0">
                        <label>
                            <input type="checkbox" name="free_shipping" value="1" @checked(old('free_shipping', $product?->free_shipping ?? false))>
                            FREE Shipping for this product (All Zones)
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="checkbox">
                        <input type="hidden" name="cod_available" value="0">
                        <label>
                            <input type="checkbox" name="cod_available" value="1" @checked(old('cod_available', $product?->cod_available ?? true))>
                            Cash on Delivery available for this product
                        </label>
                    </div>
                </div>
            </div>

            <h4 class="form-section">Delivery &amp; Returns</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="shipping_days">Days to dispatch</label>
                        <input type="number" id="shipping_days" name="shipping_days" class="form-control" value="{{ old('shipping_days', $product?->shipping_days) }}" min="0" max="365">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label" for="out_of_stock_days">Days to dispatch when out of stock</label>
                        <input type="number" id="out_of_stock_days" name="out_of_stock_days" class="form-control" value="{{ old('out_of_stock_days', $product?->out_of_stock_days) }}" min="0" max="365">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="checkbox" style="margin-top: 25px;">
                        <input type="hidden" name="is_returnable" value="0">
                        <label>
                            <input type="checkbox" id="is_returnable" name="is_returnable" value="1" @checked($isReturnable)>
                            Returnable product
                        </label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label class="control-label" for="return_days">Days allowed to return</label>
                        <input type="number" id="return_days" name="return_days" class="form-control" value="{{ old('return_days', $product?->return_days) }}" min="1" max="365" placeholder="Store default: {{ $defaultReturnDays }} days" @disabled(! $isReturnable)>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-tag"></i>
                <span class="caption-subject bold uppercase">Labels</span>
            </div>
        </div>
        <div class="portlet-body">
            <div class="form-group form-md-radios">
                <label>Show Product Label as:</label>
                <div class="md-radio-inline">
                    <div class="md-radio">
                        <input type="radio" id="radio6" name="label" value="popular" @checked($selectedLabel === 'popular') class="md-radiobtn">
                        <label for="radio6">
                        <span class="inc"></span>
                        <span class="check"></span>
                        <span class="box"></span>
                        Popular </label>
                    </div>
                    <div class="md-radio">
                        <input type="radio" id="radio7" name="label" value="new" @checked($selectedLabel === 'new') class="md-radiobtn">
                        <label for="radio7">
                        <span class="inc"></span>
                        <span class="check"></span>
                        <span class="box"></span>
                        New </label>
                    </div>
                    <div class="md-radio">
                        <input type="radio" id="radio8" name="label" value="custom" @checked($selectedLabel === 'custom') class="md-radiobtn">
                        <label for="radio8">
                        <span class="inc"></span>
                        <span class="check"></span>
                        <span class="box"></span>
                        Custom </label>
                    </div>
                </div>
                <div id="custom-label-field" class="form-group" style="margin-top: 2%;" @unless($selectedLabel === 'custom') hidden @endunless>
                    <input type="text" id="custom_label" name="custom_label" class="form-control" value="{{ old('custom_label', $product?->custom_label) }}" placeholder="Custom label" maxlength="50">
                </div>
            </div>
        </div>
    </div>

    <div class="portlet light bordered">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-globe"></i>
                <span class="caption-subject bold uppercase">Search Engines Friendly</span>
                <p class="caption-helper" style="margin-top: 2%">Define product page title, meta keywords and meta description to list your page in search engines.</p>
            </div>
            <div class="actions">
                <a
                    id="toggle-product-seo-details"
                    href="#product-seo-details"
                    data-toggle="collapse"
                    aria-expanded="false"
                    aria-controls="product-seo-details"
                >Show Details</a>
            </div>
        </div>
        <div class="portlet-body">
            <div id="product-seo-details" class="collapse">
                <div class="form-group">
                    <label class="control-label" for="seo_url">SEO URL</label>
                    <div class="input-group">
                        <span class="input-group-addon">https://shambhavivastra.com/products/</span>
                        <input type="text" id="seo_url" name="seo_url" class="form-control" value="{{ old('seo_url', $product?->seo_url) }}" maxlength="255">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="meta_title">Meta Title</label>
                    <input type="text" id="meta_title" name="meta_title" class="form-control" value="{{ old('meta_title', $product?->meta_title) }}" maxlength="255">
                </div>
                <div class="form-group">
                    <label class="control-label" for="meta_keywords">Meta Keywords</label>
                    <input type="text" id="meta_keywords" name="meta_keywords" class="form-control" value="{{ old('meta_keywords', $product?->meta_keywords) }}" maxlength="1000">
                </div>
                <div class="form-group mb-0">
                    <label class="control-label" for="meta_description">Meta Description</label>
                    <textarea id="meta_description" name="meta_description" class="form-control" rows="3" maxlength="1000">{{ old('meta_description', $product?->meta_description) }}</textarea>
                </div>
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" id="custom_seo_url" name="custom_seo_url" value="1" @checked($customSeoUrl)>
                    I would like to change product page URL (SEO URL)
                </label>
            </div>
            <hr>
            <div class="form-group mb-0">
                <label class="control-label">Search engine listing preview</label>
                <div class="product-search-preview">
                    <strong id="seo-preview-title">{{ old('meta_title', $product?->meta_title ?? old('product_name', 'Product Title')) }}</strong>
                    <div class="text-success">https://shambhavivastra.com/products/<span id="seo-preview-url">{{ old('seo_url', $product?->seo_url ?? 'product-url') }}</span></div>
                    <span id="seo-preview-description">Product description will appear here.</span>
                </div>
            </div>
        </div>
    </div>

    <div class="form-actions right">
        <a href="{{ route('admin.products.index') }}" class="btn default">Cancel</a>
        <button type="submit" name="action" value="draft" class="btn green-haze">
            <i class="fa fa-check"></i> Save Draft
        </button>
        <button type="submit" name="action" value="publish" class="btn blue">
            <i class="fa fa-upload"></i> {{ $product ? 'Update & Publish' : 'Publish Product' }}
        </button>
    </div>
</div>

@push('styles')
    <style>
        .product-image-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .product-image-grid-item {
            min-width: 0;
        }

        .product-image-slot {
            position: relative;
        }

        .product-image-upload {
            position: relative;
            display: flex;
            aspect-ratio: 1 / 1;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px dashed #c2cad8;
            background: #f5f5f5;
            color: #8b9299;
            cursor: pointer;
            text-align: center;
        }

        .product-image-upload-prompt {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            padding: 12px;
        }

        .product-image-upload-prompt .fa {
            font-size: 30px;
        }

        .product-image-preview {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-image-preview[hidden],
        .product-image-upload-prompt[hidden],
        .product-image-remove[hidden] {
            display: none;
        }

        .product-image-remove {
            position: absolute;
            top: 0;
            right: 0;
            z-index: 2;
            width: 40px;
            height: 40px;
            border: 0;
            background: #666;
            color: #e53935;
            font-size: 18px;
        }

        .product-image-remove:hover,
        .product-image-remove:focus {
            background: #4f4f4f;
            color: #ff5252;
        }

        .product-main-image {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
            color: #555;
            cursor: pointer;
        }

        .product-main-image[hidden] {
            display: none;
        }

        .product-main-image-radio {
            width: 20px;
            height: 20px;
            margin: 0;
            accent-color: #4caf50;
            cursor: pointer;
        }

        .product-image-input {
            position: absolute;
            width: 1px;
            height: 1px;
            opacity: 0;
            pointer-events: none;
        }

        @media (max-width: 991px) {
            .product-image-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .product-image-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }
        }

        .product-search-preview {
            padding: 12px 0;
        }

        .product-search-preview strong {
            display: block;
            color: #1a0dab;
            font-size: 18px;
            font-weight: 400;
        }

        .visibility-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 15px;
        }

        .visibility-toggle .control-label {
            margin: 0;
            font-weight: 600;
        }

        .publish-schedule {
            padding: 16px;
            border: 1px solid #e1e5ec;
            border-radius: 4px;
            background-color: #f5f5f5;
        }

        .publish-schedule[hidden] {
            display: none;
        }

        .publish-schedule > .control-label {
            display: block;
            margin-bottom: 8px;
        }

        .publish-schedule .input-group {
            max-width: 100%;
        }

        .publish-schedule input[type="datetime-local"] {
            min-height: 42px;
        }

        .visibility-scope .radio {
            margin-left: 0;
        }

        .generated-variants-section {
            margin-top: 28px;
        }

        .generated-variants-section[hidden] {
            display: none;
        }

        .generated-variants-table {
            min-width: 1260px;
            margin-bottom: 0;
        }

        .generated-variants-table th {
            white-space: nowrap;
            vertical-align: middle !important;
        }

        .generated-variants-table td {
            vertical-align: middle !important;
        }

        .generated-variants-table .form-control {
            min-width: 82px;
        }

        .generated-variants-table .variant-combination-name {
            min-width: 150px;
            white-space: nowrap;
        }

        .generated-variants-table .variant-row-toggle,
        .generated-variants-table .variant-main-toggle {
            width: 18px;
            height: 18px;
            margin: 0;
        }
    </style>
@endpush

@push('scripts')
    <script>
        jQuery(function ($) {
            const sellingPrice = document.getElementById('selling_price');
            const discount = document.getElementById('discount');
            const netPrice = document.getElementById('net_price');
            const taxableProduct = document.getElementById('checkbox31');
            const taxRate = document.getElementById('tax_rate');

            const updateNetPrice = function () {
                if (sellingPrice.value === '') {
                    netPrice.value = '';
                    return;
                }

                const sellingPriceAmount = Number(sellingPrice.value);
                const discountAmount = discount.value === '' ? 0 : Number(discount.value);
                netPrice.value = Math.max(0, sellingPriceAmount - discountAmount).toFixed(2);
            };

            [sellingPrice, discount].forEach(function (field) {
                field.addEventListener('input', updateNetPrice);
                field.addEventListener('change', updateNetPrice);
            });
            updateNetPrice();

            const updateTaxRateAvailability = function () {
                taxRate.disabled = !taxableProduct.checked;
            };

            taxableProduct.addEventListener('change', updateTaxRateAvailability);
            updateTaxRateAvailability();

            $('#select2_sample_modal_5').select2({
                theme: 'bootstrap',
                placeholder: 'Search and select categories...',
                allowClear: true,
                templateResult: function (category) {
                    if (category.element && category.element.selected) {
                        return null;
                    }

                    return category.text;
                },
            });

            const productName = document.getElementById('product_name');
            const productDescription = document.getElementById('summernote_1');
            const metaTitle = document.getElementById('meta_title');
            const metaKeywords = document.getElementById('meta_keywords');
            const metaDescription = document.getElementById('meta_description');
            const seoUrl = document.getElementById('seo_url');
            const customSeoUrl = document.getElementById('custom_seo_url');
            const seoDetailsToggle = document.getElementById('toggle-product-seo-details');
            const seoDetails = document.getElementById('product-seo-details');
            const seoPreviewTitle = document.getElementById('seo-preview-title');
            const seoPreviewUrl = document.getElementById('seo-preview-url');
            const seoPreviewDescription = document.getElementById('seo-preview-description');

            const slugifyProductName = function (value) {
                return value
                    .trim()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .replace(/[^\p{L}\p{N}]+/gu, '-')
                    .replace(/^-|-$/g, '');
            };

            const updateSeoPreview = function () {
                seoPreviewTitle.textContent =
                    metaTitle.value || productName.value || 'Product Title';
                seoPreviewUrl.textContent =
                    seoUrl.value || 'product-url';

                const descriptionHtml = $.fn.summernote
                    && $(productDescription).next('.note-editor').length
                    ? $(productDescription).code()
                    : productDescription.value;
                const parsedDescription = new DOMParser().parseFromString(
                    descriptionHtml,
                    'text/html'
                );
                seoPreviewDescription.textContent =
                    parsedDescription.body.textContent.trim()
                    || 'Product description will appear here.';
            };

            const updateSeoFieldsFromProductName = function () {
                const name = productName.value.trim();
                metaTitle.value = name;
                metaKeywords.value = name;
                metaDescription.value = name;

                if (!customSeoUrl.checked) {
                    seoUrl.value = slugifyProductName(name);
                }

                updateSeoPreview();
            };

            productName.addEventListener('input', updateSeoFieldsFromProductName);
            [metaTitle, metaDescription, seoUrl].forEach(function (field) {
                field.addEventListener('input', updateSeoPreview);
            });

            const updateDescriptionPreview = function () {
                updateSeoPreview();
            };

            $(seoDetails)
                .on('shown.bs.collapse', function () {
                    seoDetailsToggle.textContent = 'Hide Details';
                    seoDetailsToggle.setAttribute('aria-expanded', 'true');
                })
                .on('hidden.bs.collapse', function () {
                    seoDetailsToggle.textContent = 'Show Details';
                    seoDetailsToggle.setAttribute('aria-expanded', 'false');
                });

            productDescription.addEventListener('input', updateDescriptionPreview);
            productDescription.addEventListener('change', updateDescriptionPreview);
            $(productDescription).on('summernote.change', updateDescriptionPreview);
            $(productDescription)
                .next('.note-editor')
                .find('.note-editable')
                .on('input keyup paste', updateDescriptionPreview);

            customSeoUrl.addEventListener('change', function () {
                if (this.checked) {
                    $('#product-seo-details').collapse('show');
                } else {
                    seoUrl.value = slugifyProductName(productName.value);
                    updateSeoPreview();
                }
            });

            const customLabelField = document.getElementById('custom-label-field');
            const labelOptions = document.querySelectorAll('input[name="label"]');
            const updateCustomLabelVisibility = function () {
                const customLabelSelected = document.querySelector(
                    'input[name="label"]:checked'
                )?.value === 'custom';

                customLabelField.hidden = !customLabelSelected;
            };

            labelOptions.forEach(function (labelOption) {
                labelOption.addEventListener('change', updateCustomLabelVisibility);
            });
            updateCustomLabelVisibility();
            if (productName.value.trim() && metaTitle.value.trim() === '') {
                updateSeoFieldsFromProductName();
            } else {
                updateSeoPreview();
            }

            const publishSchedule = document.getElementById('publish-schedule');
            const publishScheduleToggle = document.getElementById('toggle-publish-schedule');
            const publishAt = document.getElementById('publish_at');

            publishScheduleToggle.addEventListener('click', function () {
                publishSchedule.hidden = !publishSchedule.hidden;
                this.setAttribute('aria-expanded', String(!publishSchedule.hidden));

                if (!publishSchedule.hidden) {
                    publishAt.focus();
                }
            });

            document.getElementById('open-publish-picker').addEventListener('click', function () {
                if (typeof publishAt.showPicker === 'function') {
                    publishAt.showPicker();
                    return;
                }

                publishAt.focus();
                publishAt.click();
            });

            const imageSlots = Array.from(document.querySelectorAll('[data-image-slot]'));
            let mainImageRadio = null;

            const syncMainImageRadios = function () {
                const allImageRadios = imageSlots.map(function (slot) {
                    return slot.querySelector('.product-main-image-radio');
                });
                const uploadedImageRadios = allImageRadios
                    .filter(function (radio) {
                        return !radio.closest('.product-main-image').hidden;
                    });
                const uploadedRadioSet = new Set(uploadedImageRadios);

                allImageRadios.forEach(function (radio) {
                    if (!uploadedRadioSet.has(radio)) {
                        radio.checked = false;
                    }
                });

                if (uploadedImageRadios.length === 0) {
                    mainImageRadio = null;
                    return;
                }

                if (!uploadedImageRadios.some(function (radio) {
                    return radio.checked;
                })) {
                    uploadedImageRadios[0].checked = true;
                }

                const checkedRadio = uploadedImageRadios.find(function (radio) {
                    return radio.checked;
                });
                mainImageRadio = checkedRadio ? checkedRadio.value : null;
            };

            imageSlots.forEach(function (slot) {
                const input = slot.querySelector('.product-image-input');
                const preview = slot.querySelector('.product-image-preview');
                const prompt = slot.querySelector('.product-image-upload-prompt');
                const removeButton = slot.querySelector('.product-image-remove');
                const mainImageLabel = slot.querySelector('.product-main-image');
                const mainImageInput = slot.querySelector('.product-main-image-radio');
                const existingImageInput = slot.querySelector('.product-existing-image-input');
                let previewUrl = null;

                const releaseExistingImage = function () {
                    if (existingImageInput) {
                        existingImageInput.disabled = true;
                    }
                };

                input.addEventListener('change', function () {
                    const file = this.files && this.files[0];
                    if (!file) {
                        return;
                    }

                    if (previewUrl) {
                        URL.revokeObjectURL(previewUrl);
                    }
                    releaseExistingImage();
                    previewUrl = URL.createObjectURL(file);
                    preview.src = previewUrl;
                    preview.hidden = false;
                    prompt.hidden = true;
                    removeButton.hidden = false;
                    mainImageLabel.hidden = false;
                    syncMainImageRadios();
                });

                removeButton.addEventListener('click', function () {
                    releaseExistingImage();
                    input.value = '';
                    preview.removeAttribute('src');
                    preview.hidden = true;
                    prompt.hidden = false;
                    removeButton.hidden = true;
                    mainImageLabel.hidden = true;
                    mainImageInput.checked = false;

                    if (previewUrl) {
                        URL.revokeObjectURL(previewUrl);
                        previewUrl = null;
                    }
                    syncMainImageRadios();
                });

                mainImageInput.addEventListener('change', function () {
                    if (this.checked) {
                        mainImageRadio = this.value;
                    }
                });
            });

            syncMainImageRadios();

            const hasVariants = document.getElementById('has_variants');
            const variantPanel = document.getElementById('variant-options-panel');
            const generateVariants = document.getElementById('generate_variants');
            const variantRows = Array.from(document.querySelectorAll('.variant-option-row'));
            const generatedVariantsSection = document.getElementById('generated-variants-section');
            const generatedVariantsBody = document.getElementById('generated-variants-body');
            const initialVariantValues = new Map(
                Object.entries(JSON.parse(generatedVariantsBody.dataset.initialVariants || '{}'))
            );
            let mainVariantKey = generatedVariantsBody.dataset.mainVariantKey || null;

            const captureGeneratedVariantValues = function () {
                const valuesByKey = new Map();

                generatedVariantsBody.querySelectorAll('tr[data-variant-key]').forEach(function (row) {
                    const values = {};
                    row.querySelectorAll('[data-field]').forEach(function (field) {
                        values[field.dataset.field] = field.type === 'checkbox'
                            ? field.checked
                            : field.value;
                    });

                    valuesByKey.set(row.dataset.variantKey, values);
                    if (row.querySelector('.variant-main-toggle').checked) {
                        mainVariantKey = row.dataset.variantKey;
                    }
                });

                return valuesByKey;
            };

            const getVariantCombinations = function () {
                const selectedOptions = variantRows
                    .filter(function (row) {
                        return row.querySelector('.variant-row-enabled').checked;
                    })
                    .map(function (row) {
                        const typeSelect = row.querySelector('.variant-type-select');
                        const valuesSelect = row.querySelector('.variant-values-select');
                        const selectedValueIds = $(valuesSelect).val() || [];

                        if (!typeSelect.value || selectedValueIds.length === 0) {
                            return [];
                        }

                        return selectedValueIds.map(function (valueId) {
                            const valueOption = Array.from(valuesSelect.options).find(function (option) {
                                return option.value === valueId;
                            });

                            return {
                                typeId: typeSelect.value,
                                valueId: valueId,
                                valueName: valueOption ? valueOption.text : '',
                            };
                        });
                    })
                    .filter(function (options) {
                        return options.length > 0;
                    });

                if (selectedOptions.length === 0) {
                    return [];
                }

                return selectedOptions.reduce(function (combinations, options) {
                    return combinations.flatMap(function (combination) {
                        return options.map(function (option) {
                            return combination.concat(option);
                        });
                    });
                }, [[]]).map(function (combination) {
                    return {
                        key: combination.slice().sort(function (first, second) {
                            return Number(first.typeId) - Number(second.typeId);
                        }).map(function (option) {
                            return option.typeId + ':' + option.valueId;
                        }).join('|'),
                        label: combination.map(function (option) {
                            return option.valueName;
                        }).join(' / '),
                    };
                });
            };

            const addVariantInput = function (row, index, field, type, value, checked) {
                const cell = document.createElement('td');
                const input = document.createElement('input');
                input.type = type;
                input.name = 'product_variants[' + index + '][' + field + ']';
                input.dataset.field = field;
                input.className = type === 'checkbox'
                    ? 'variant-row-toggle'
                    : 'form-control input-sm';

                if (type === 'checkbox') {
                    input.value = '1';
                    input.checked = checked;
                } else {
                    input.value = value || '';
                    if (type === 'number') {
                        input.min = '0';
                        input.step = field === 'weight' ? '0.001' : '0.01';
                    }
                }

                cell.appendChild(input);
                row.appendChild(cell);

                return input;
            };

            const renderVariantCombinations = function () {
                const savedValues = captureGeneratedVariantValues();
                const combinations = getVariantCombinations();
                const sellingPrice = document.getElementById('selling_price').value;
                const discountPrice = document.getElementById('discount').value;
                const netPrice = document.getElementById('net_price').value
                    || (sellingPrice !== '' && discountPrice !== ''
                        ? Math.max(0, Number(sellingPrice) - Number(discountPrice)).toFixed(2)
                        : sellingPrice);
                const baseSku = document.getElementById('sku').value;
                const defaults = {
                    enabled: true,
                    product_price: sellingPrice,
                    discount_price: discountPrice,
                    net_price: netPrice,
                    sku: '',
                    barcode: '',
                    weight: document.getElementById('weight').value,
                    quantity: document.getElementById('stock_quantity').value,
                };

                generatedVariantsBody.replaceChildren();
                if (combinations.length === 0) {
                    generatedVariantsSection.hidden = true;
                    return;
                }

                if (!combinations.some(function (combination) {
                    return combination.key === mainVariantKey;
                })) {
                    mainVariantKey = combinations[0].key;
                }

                combinations.forEach(function (combination, index) {
                    const row = document.createElement('tr');
                    row.dataset.variantKey = combination.key;
                    const existingValues = savedValues.get(combination.key)
                        || initialVariantValues.get(combination.key)
                        || {};
                    const rowValues = Object.assign({}, defaults, existingValues);
                    const includeCell = document.createElement('td');
                    const includeInput = document.createElement('input');
                    includeInput.type = 'checkbox';
                    includeInput.name = 'product_variants[' + index + '][enabled]';
                    includeInput.dataset.field = 'enabled';
                    includeInput.className = 'variant-row-toggle';
                    includeInput.value = '1';
                    includeInput.checked = rowValues.enabled !== false;
                    includeCell.appendChild(includeInput);

                    const keyInput = document.createElement('input');
                    keyInput.type = 'hidden';
                    keyInput.name = 'product_variants[' + index + '][key]';
                    keyInput.value = combination.key;
                    includeCell.appendChild(keyInput);
                    row.appendChild(includeCell);

                    const combinationCell = document.createElement('td');
                    combinationCell.className = 'variant-combination-name';
                    combinationCell.textContent = combination.label;
                    row.appendChild(combinationCell);

                    ['product_price', 'discount_price', 'net_price'].forEach(function (field) {
                        addVariantInput(row, index, field, 'number', rowValues[field]);
                    });

                    const skuValue = Object.prototype.hasOwnProperty.call(existingValues, 'sku')
                        ? rowValues.sku
                        : (baseSku ? baseSku + '-' + (index + 1) : '');
                    addVariantInput(row, index, 'sku', 'text', skuValue);
                    addVariantInput(row, index, 'barcode', 'text', rowValues.barcode);
                    addVariantInput(row, index, 'weight', 'number', rowValues.weight);
                    addVariantInput(row, index, 'quantity', 'number', rowValues.quantity);

                    const mainCell = document.createElement('td');
                    const mainInput = document.createElement('input');
                    mainInput.type = 'radio';
                    mainInput.name = 'main_variant_key';
                    mainInput.value = combination.key;
                    mainInput.className = 'variant-main-toggle';
                    mainInput.checked = mainVariantKey
                        ? mainVariantKey === combination.key
                        : index === 0;
                    mainInput.addEventListener('change', function () {
                        mainVariantKey = combination.key;
                    });
                    mainCell.appendChild(mainInput);
                    row.appendChild(mainCell);

                    const rowPrice = row.querySelector('[data-field="product_price"]');
                    const rowDiscount = row.querySelector('[data-field="discount_price"]');
                    const rowNetPrice = row.querySelector('[data-field="net_price"]');
                    const updateRowNetPrice = function () {
                        rowNetPrice.value = rowPrice.value === ''
                            ? ''
                            : Math.max(0, Number(rowPrice.value) - Number(rowDiscount.value || 0)).toFixed(2);
                    };
                    rowNetPrice.readOnly = true;
                    rowPrice.addEventListener('input', updateRowNetPrice);
                    rowDiscount.addEventListener('input', updateRowNetPrice);
                    updateRowNetPrice();

                    generatedVariantsBody.appendChild(row);
                });

                generatedVariantsSection.hidden = false;
            };

            const syncGenerateVariants = function () {
                const hasCompleteVariantOption = hasVariants.checked && variantRows.some(function (row) {
                    return row.querySelector('.variant-row-enabled').checked
                        && row.querySelector('.variant-type-select').value
                        && ($(row.querySelector('.variant-values-select')).val() || []).length > 0;
                });

                generateVariants.disabled = !hasVariants.checked || !hasCompleteVariantOption;
                if (!hasCompleteVariantOption) {
                    generateVariants.checked = false;
                }
                $.uniform.update($(generateVariants));

                if (generateVariants.checked) {
                    renderVariantCombinations();
                } else {
                    captureGeneratedVariantValues();
                    generatedVariantsSection.hidden = true;
                }
            };

            variantRows.forEach(function (row) {
                const rowEnabled = row.querySelector('.variant-row-enabled');
                const typeSelect = row.querySelector('.variant-type-select');
                const valuesSelect = row.querySelector('.variant-values-select');

                $(valuesSelect).select2({
                    theme: 'bootstrap',
                    placeholder: 'Select one or more values',
                    width: '100%',
                    closeOnSelect: false,
                });

                const populateValues = function (selectedValues) {
                    const selectedOption = typeSelect.selectedOptions[0];
                    const values = selectedOption && selectedOption.value
                        ? JSON.parse(selectedOption.dataset.values || '[]')
                        : [];

                    valuesSelect.replaceChildren();
                    values.forEach(function (value) {
                        valuesSelect.add(new Option(value.text, value.id));
                    });
                    $(valuesSelect).val(selectedValues).trigger('change');
                };

                const syncRow = function () {
                    const isEnabled = hasVariants.checked && rowEnabled.checked;
                    rowEnabled.disabled = !hasVariants.checked;
                    typeSelect.disabled = !isEnabled;
                    valuesSelect.disabled = !isEnabled || !typeSelect.value;
                    $.uniform.update($(rowEnabled));
                    $(valuesSelect).trigger('change.select2');
                };

                const initialValues = JSON.parse(valuesSelect.dataset.selectedValues || '[]');
                populateValues(initialValues);
                syncRow();
                $(typeSelect).on('change', function () {
                    populateValues([]);
                    syncRow();
                    syncGenerateVariants();
                });
                $(rowEnabled).on('change', function () {
                    syncRow();
                    syncGenerateVariants();
                });
                $(valuesSelect).on(
                    'change select2:select select2:unselect',
                    syncGenerateVariants
                );
            });

            $(hasVariants).on('change', function () {
                variantPanel.hidden = !this.checked;
                variantRows.forEach(function (row) {
                    const rowEnabled = row.querySelector('.variant-row-enabled');
                    const typeSelect = row.querySelector('.variant-type-select');
                    const valuesSelect = row.querySelector('.variant-values-select');
                    const isRowEnabled = hasVariants.checked && rowEnabled.checked;

                    rowEnabled.disabled = !hasVariants.checked;
                    typeSelect.disabled = !isRowEnabled;
                    valuesSelect.disabled = !isRowEnabled || !typeSelect.value;
                    $.uniform.update($(rowEnabled));
                    $(valuesSelect).trigger('change.select2');
                });
                syncGenerateVariants();
            });

            $(generateVariants).on('change', function () {
                if (this.checked) {
                    renderVariantCombinations();
                } else {
                    captureGeneratedVariantValues();
                    generatedVariantsSection.hidden = true;
                }
            });

            syncGenerateVariants();

            const stockQuantity = document.getElementById('stock_quantity');
            const trackInventory = document.getElementById('checkbox30');
            const syncStockQuantity = function () {
                stockQuantity.disabled = !trackInventory.checked || hasVariants.checked;
            };
            $(trackInventory).on('change', syncStockQuantity);
            $(hasVariants).on('change', syncStockQuantity);
            syncStockQuantity();

            const isReturnable = document.getElementById('is_returnable');
            const returnDays = document.getElementById('return_days');
            $(isReturnable).on('change', function () {
                returnDays.disabled = !this.checked;
            });

            const weightInput = document.getElementById('weight');
            const dimensionInputs = ['length', 'breadth', 'height'].map(function (id) {
                return document.getElementById(id);
            });
            const shippingPackage = document.getElementById('shipping_package_id');
            const volumetricPreview = document.getElementById('volumetric-weight-preview');
            const volumetricDivisor = Number(volumetricPreview.dataset.volumetricDivisor) || 5000;

            const updateVolumetricPreview = function () {
                const dimensions = dimensionInputs.map(function (input) {
                    return Number(input.value);
                });

                if (dimensions.some(function (dimension) { return !dimension; })) {
                    volumetricPreview.textContent = '';
                    return;
                }

                const volumetricWeight = (dimensions[0] * dimensions[1] * dimensions[2]) / volumetricDivisor;
                const chargeableWeight = Math.max(volumetricWeight, Number(weightInput.value) || 0);
                volumetricPreview.textContent = 'Volumetric weight: ' + volumetricWeight.toFixed(3)
                    + ' kg · Chargeable weight: ' + chargeableWeight.toFixed(3) + ' kg';
            };

            shippingPackage.addEventListener('change', function () {
                const option = this.selectedOptions[0];

                if (option && option.value) {
                    dimensionInputs.forEach(function (input) {
                        if (input.value === '') {
                            input.value = option.dataset[input.id];
                        }
                    });
                }

                updateVolumetricPreview();
            });
            dimensionInputs.concat(weightInput).forEach(function (input) {
                input.addEventListener('input', updateVolumetricPreview);
            });
            updateVolumetricPreview();

            // Summernote v0.6 reads its HTML with .code(); .summernote('code') returns undefined.
            $(productDescription).closest('form').on('submit', function () {
                if ($.fn.summernote && $(productDescription).next('.note-editor').length) {
                    productDescription.value = $(productDescription).code();
                }
            });

            updateSeoPreview();
        });
    </script>
@endpush

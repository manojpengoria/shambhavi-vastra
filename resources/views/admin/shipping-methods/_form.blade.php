<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $shippingMethod?->name) }}" placeholder="e.g. Standard, Express" maxlength="100" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code', $shippingMethod?->code) }}" placeholder="e.g. STD" maxlength="20" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">Customer Text</label> <small>(shown at checkout)</small>
                <input type="text" name="customer_text" class="form-control" value="{{ old('customer_text', $shippingMethod?->customer_text) }}" placeholder="e.g. Delivered in 5-7 business days" maxlength="150">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Display Order <span class="text-danger">*</span></label>
                <input type="number" name="display_order" class="form-control" value="{{ old('display_order', $shippingMethod?->display_order ?? 1) }}" min="1" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="is_active" class="form-control" required>
                    <option value="1" @selected((string) old('is_active', (int) ($shippingMethod?->is_active ?? true)) === '1')>Active</option>
                    <option value="0" @selected((string) old('is_active', (int) ($shippingMethod?->is_active ?? true)) === '0')>Inactive</option>
                </select>
            </div>
        </div>
    </div>
</div>

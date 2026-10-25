<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $shippingClass?->name) }}" placeholder="e.g. Light, Heavy" maxlength="100" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code', $shippingClass?->code) }}" placeholder="e.g. HEAVY" maxlength="20" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Extra Charge (₹) <span class="text-danger">*</span></label>
                <input type="number" name="extra_charge" class="form-control" value="{{ old('extra_charge', $shippingClass?->extra_charge ?? 0) }}" min="0" step="0.01" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="is_active" class="form-control" required>
                    <option value="1" @selected((string) old('is_active', (int) ($shippingClass?->is_active ?? true)) === '1')>Active</option>
                    <option value="0" @selected((string) old('is_active', (int) ($shippingClass?->is_active ?? true)) === '0')>Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">Description</label>
                <input type="text" name="description" class="form-control" value="{{ old('description', $shippingClass?->description) }}" placeholder="e.g. Products above 5 kg" maxlength="255">
            </div>
        </div>
    </div>
</div>

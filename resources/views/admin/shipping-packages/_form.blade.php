<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $shippingPackage?->name) }}" placeholder="e.g. Medium box" maxlength="100" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code', $shippingPackage?->code) }}" placeholder="e.g. BOX-M" maxlength="20" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Length (cm) <span class="text-danger">*</span></label>
                <input type="number" name="length" class="form-control" value="{{ old('length', $shippingPackage?->length) }}" min="0.01" step="0.01" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Breadth (cm) <span class="text-danger">*</span></label>
                <input type="number" name="breadth" class="form-control" value="{{ old('breadth', $shippingPackage?->breadth) }}" min="0.01" step="0.01" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Height (cm) <span class="text-danger">*</span></label>
                <input type="number" name="height" class="form-control" value="{{ old('height', $shippingPackage?->height) }}" min="0.01" step="0.01" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Box Weight (kg) <span class="text-danger">*</span></label> <small>(empty box)</small>
                <input type="number" name="box_weight" class="form-control" value="{{ old('box_weight', $shippingPackage?->box_weight ?? 0) }}" min="0" step="0.001" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Max Weight (kg)</label>
                <input type="number" name="max_weight" class="form-control" value="{{ old('max_weight', $shippingPackage?->max_weight) }}" min="0" step="0.001">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Packaging Cost (₹)</label>
                <input type="number" name="cost" class="form-control" value="{{ old('cost', $shippingPackage?->cost) }}" min="0" step="0.01">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="is_active" class="form-control" required>
                    <option value="1" @selected((string) old('is_active', (int) ($shippingPackage?->is_active ?? true)) === '1')>Active</option>
                    <option value="0" @selected((string) old('is_active', (int) ($shippingPackage?->is_active ?? true)) === '0')>Inactive</option>
                </select>
            </div>
        </div>
    </div>
</div>

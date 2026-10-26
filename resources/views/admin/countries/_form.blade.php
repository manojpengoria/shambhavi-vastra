<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Country Name <span class="text-danger">*</span></label>
                <input type="text" name="country_name" class="form-control" value="{{ old('country_name', $country?->country_name) }}" placeholder="e.g. India" maxlength="100" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Country Code <span class="text-danger">*</span></label> <small>(ISO 3166-1 alpha-2)</small>
                <input type="text" name="country_code" class="form-control text-uppercase" value="{{ old('country_code', $country?->country_code) }}" placeholder="e.g. IN" maxlength="2" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Dial Code <span class="text-danger">*</span></label>
                <input type="text" name="dial_code" class="form-control" value="{{ old('dial_code', $country?->dial_code) }}" placeholder="e.g. +91" maxlength="10" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="707" @selected((string) old('status', $country?->status ?? 707) === '707')>Active</option>
                    <option value="505" @selected((string) old('status', $country?->status ?? 707) === '505')>Inactive</option>
                </select>
            </div>
        </div>
    </div>
</div>

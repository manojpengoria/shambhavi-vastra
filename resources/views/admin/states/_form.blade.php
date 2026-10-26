<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Country <span class="text-danger">*</span></label>
                <select name="country_id" class="form-control" required>
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" @selected((string) old('country_id', $state?->country_id) === (string) $country->id)>
                            {{ $country->country_name }} ({{ $country->country_code }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">State Name <span class="text-danger">*</span></label>
                <input type="text" name="state_name" class="form-control" value="{{ old('state_name', $state?->state_name) }}" placeholder="e.g. Rajasthan" maxlength="100" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">State Code <span class="text-danger">*</span></label> <small>(ISO 3166-2, e.g. IN-RJ)</small>
                <input type="text" name="state_code" class="form-control text-uppercase" value="{{ old('state_code', $state?->state_code) }}" placeholder="e.g. IN-RJ" maxlength="10" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">GST State Code</label> <small>(2 digits, e.g. 08)</small>
                <input type="text" name="gst_state_code" class="form-control" value="{{ old('gst_state_code', $state?->gst_state_code) }}" placeholder="e.g. 08" maxlength="2">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Type <span class="text-danger">*</span></label>
                <select name="state_type" class="form-control" required>
                    <option value="STATE" @selected(old('state_type', $state?->state_type ?? 'STATE') === 'STATE')>State</option>
                    <option value="UT" @selected(old('state_type', $state?->state_type ?? 'STATE') === 'UT')>Union Territory</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Capital</label>
                <input type="text" name="capital" class="form-control" value="{{ old('capital', $state?->capital) }}" placeholder="e.g. Jaipur" maxlength="100">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="707" @selected((string) old('status', $state?->status ?? 707) === '707')>Active</option>
                    <option value="505" @selected((string) old('status', $state?->status ?? 707) === '505')>Inactive</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $state?->sort_order ?? 0) }}" min="0" required>
            </div>
        </div>
    </div>
</div>

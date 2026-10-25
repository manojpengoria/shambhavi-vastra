<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">City <span class="text-danger">*</span></label>
                <select name="city_id" class="form-control js-location-select" data-lookup-url="{{ route('admin.locations.lookup.cities') }}" data-placeholder="Search City" required>
                    <option value=""></option>
                    @if($selectedCity)
                        <option value="{{ $selectedCity->id }}" selected>{{ $selectedCity->selectLabel() }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Postal Code <span class="text-danger">*</span></label> <small>(PIN code)</small>
                <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $postalCode?->postal_code) }}" placeholder="e.g. 302001" maxlength="10" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Post Office Name</label>
                <input type="text" name="post_office_name" class="form-control" value="{{ old('post_office_name', $postalCode?->post_office_name) }}" placeholder="e.g. Jaipur GPO" maxlength="150">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Office Type</label>
                <select name="office_type" class="form-control">
                    <option value="">Select Office Type</option>
                    @foreach(\App\Models\PostalCode::OFFICE_TYPES as $value => $label)
                        <option value="{{ $value }}" @selected(old('office_type', $postalCode?->office_type) === $value)>{{ $label }} ({{ $value }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Delivery</label>
                <select name="delivery_status" class="form-control" required>
                    <option value="707" @selected((string) old('delivery_status', $postalCode?->delivery_status ?? 707) === '707')>Available</option>
                    <option value="505" @selected((string) old('delivery_status', $postalCode?->delivery_status ?? 707) === '505')>Unavailable</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="707" @selected((string) old('status', $postalCode?->status ?? 707) === '707')>Active</option>
                    <option value="505" @selected((string) old('status', $postalCode?->status ?? 707) === '505')>Inactive</option>
                </select>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.location-select')

<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">City <span class="text-danger">*</span></label>
                <select name="city_id" id="area_city_id" class="form-control js-location-select" data-lookup-url="{{ route('admin.locations.lookup.cities') }}" data-placeholder="Search City" required>
                    <option value=""></option>
                    @if($selectedCity)
                        <option value="{{ $selectedCity->id }}" selected>{{ $selectedCity->selectLabel() }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Postal Code</label> <small>(filtered by selected city)</small>
                <select name="postal_code_id" id="area_postal_code_id" class="form-control js-location-select" data-lookup-url="{{ route('admin.locations.lookup.postal-codes') }}" data-placeholder="Search Postal Code" data-depends-on="#area_city_id" data-depends-param="city_id">
                    <option value=""></option>
                    @if($selectedPostalCode)
                        <option value="{{ $selectedPostalCode->id }}" selected>{{ $selectedPostalCode->selectLabel() }}</option>
                    @endif
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Area Name <span class="text-danger">*</span></label>
                <input type="text" name="area_name" class="form-control" value="{{ old('area_name', $area?->area_name) }}" placeholder="e.g. Malviya Nagar" maxlength="150" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Area Code <span class="text-danger">*</span></label> <small>(unique, e.g. JAI-MLV)</small>
                <input type="text" name="area_code" class="form-control text-uppercase" value="{{ old('area_code', $area?->area_code) }}" placeholder="e.g. JAI-MLV" maxlength="30" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Type <span class="text-danger">*</span></label>
                <select name="area_type" class="form-control" required>
                    @foreach(['LOCALITY' => 'Locality', 'COLONY' => 'Colony', 'SECTOR' => 'Sector', 'VILLAGE' => 'Village'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('area_type', $area?->area_type ?? 'LOCALITY') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Delivery</label>
                <select name="delivery_available" class="form-control" required>
                    <option value="707" @selected((string) old('delivery_available', $area?->delivery_available ?? 707) === '707')>Available</option>
                    <option value="505" @selected((string) old('delivery_available', $area?->delivery_available ?? 707) === '505')>Unavailable</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Latitude</label>
                <input type="number" name="latitude" class="form-control" value="{{ old('latitude', $area?->latitude) }}" placeholder="e.g. 26.8548890" step="0.0000001" min="-90" max="90">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Longitude</label>
                <input type="number" name="longitude" class="form-control" value="{{ old('longitude', $area?->longitude) }}" placeholder="e.g. 75.8243100" step="0.0000001" min="-180" max="180">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="707" @selected((string) old('status', $area?->status ?? 707) === '707')>Active</option>
                    <option value="505" @selected((string) old('status', $area?->status ?? 707) === '505')>Inactive</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $area?->sort_order ?? 0) }}" min="0" required>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.location-select')

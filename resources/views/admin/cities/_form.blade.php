<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">State <span class="text-danger">*</span></label>
                <select name="state_id" class="form-control js-location-select" data-lookup-url="{{ route('admin.locations.lookup.states') }}" data-placeholder="Search State" required>
                    <option value=""></option>
                    @if($selectedState)
                        <option value="{{ $selectedState->id }}" selected>{{ $selectedState->selectLabel() }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">City Name <span class="text-danger">*</span></label>
                <input type="text" name="city_name" class="form-control" value="{{ old('city_name', $city?->city_name) }}" placeholder="e.g. Jaipur" maxlength="150" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">City Code <span class="text-danger">*</span></label> <small>(unique, e.g. JAI)</small>
                <input type="text" name="city_code" class="form-control text-uppercase" value="{{ old('city_code', $city?->city_code) }}" placeholder="e.g. JAI" maxlength="20" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Type <span class="text-danger">*</span></label>
                <select name="city_type" class="form-control" required>
                    @foreach(['CITY' => 'City', 'TOWN' => 'Town', 'VILLAGE' => 'Village'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('city_type', $city?->city_type ?? 'CITY') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Latitude</label>
                <input type="number" name="latitude" class="form-control" value="{{ old('latitude', $city?->latitude) }}" placeholder="e.g. 26.9124336" step="0.0000001" min="-90" max="90">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Longitude</label>
                <input type="number" name="longitude" class="form-control" value="{{ old('longitude', $city?->longitude) }}" placeholder="e.g. 75.7872709" step="0.0000001" min="-180" max="180">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="707" @selected((string) old('status', $city?->status ?? 707) === '707')>Active</option>
                    <option value="505" @selected((string) old('status', $city?->status ?? 707) === '505')>Inactive</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $city?->sort_order ?? 0) }}" min="0" required>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.location-select')

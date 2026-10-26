<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $warehouse?->name) }}" placeholder="e.g. Main godown" maxlength="100" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code', $warehouse?->code) }}" placeholder="e.g. WH-JPR" maxlength="20" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Contact Person <span class="text-danger">*</span></label>
                <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $warehouse?->contact_person) }}" maxlength="100" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Mobile <span class="text-danger">*</span></label>
                <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $warehouse?->mobile) }}" placeholder="e.g. 9876543210" maxlength="15" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $warehouse?->email) }}" maxlength="150">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">Address <span class="text-danger">*</span></label>
                <input type="text" name="address_line" class="form-control" value="{{ old('address_line', $warehouse?->address_line) }}" placeholder="Building, street" maxlength="255" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">State <span class="text-danger">*</span></label>
                <select name="state_id" id="warehouse_state_id" class="form-control js-location-select" data-lookup-url="{{ route('admin.locations.lookup.states') }}" data-placeholder="Search State" required>
                    <option value=""></option>
                    @if($selectedState)
                        <option value="{{ $selectedState->id }}" selected>{{ $selectedState->selectLabel() }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">City <span class="text-danger">*</span></label>
                <select name="city_id" id="warehouse_city_id" class="form-control js-location-select" data-lookup-url="{{ route('admin.locations.lookup.cities') }}" data-placeholder="Search City" data-depends-on="#warehouse_state_id" data-depends-param="state_id" required>
                    <option value=""></option>
                    @if($selectedCity)
                        <option value="{{ $selectedCity->id }}" selected>{{ $selectedCity->selectLabel() }}</option>
                    @endif
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Postal Code <span class="text-danger">*</span></label>
                <select name="postal_code_id" class="form-control js-location-select" data-lookup-url="{{ route('admin.locations.lookup.postal-codes') }}" data-placeholder="Search Postal Code" data-depends-on="#warehouse_city_id" data-depends-param="city_id" required>
                    <option value=""></option>
                    @if($selectedPostalCode)
                        <option value="{{ $selectedPostalCode->id }}" selected>{{ $selectedPostalCode->selectLabel() }}</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Area</label>
                <select name="area_id" class="form-control js-location-select" data-lookup-url="{{ route('admin.locations.lookup.areas') }}" data-placeholder="Search Area" data-depends-on="#warehouse_city_id" data-depends-param="city_id">
                    <option value=""></option>
                    @if($selectedArea)
                        <option value="{{ $selectedArea->id }}" selected>{{ $selectedArea->selectLabel() }}</option>
                    @endif
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">GSTIN</label>
                <input type="text" name="gstin" class="form-control text-uppercase" value="{{ old('gstin', $warehouse?->gstin) }}" placeholder="e.g. 08ABCDE1234F1Z5" maxlength="15">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Pickup Time Slot</label>
                <input type="text" name="pickup_time_slot" class="form-control" value="{{ old('pickup_time_slot', $warehouse?->pickup_time_slot) }}" placeholder="e.g. 10 AM - 2 PM" maxlength="50">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Default Warehouse</label>
                <div class="checkbox">
                    <input type="hidden" name="is_default" value="0">
                    <label><input type="checkbox" name="is_default" value="1" @checked(old('is_default', $warehouse?->is_default ?? false))> Use as default pickup warehouse</label>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="is_active" class="form-control" required>
                    <option value="1" @selected((string) old('is_active', (int) ($warehouse?->is_active ?? true)) === '1')>Active</option>
                    <option value="0" @selected((string) old('is_active', (int) ($warehouse?->is_active ?? true)) === '0')>Inactive</option>
                </select>
            </div>
        </div>
    </div>
</div>

@include('admin.partials.location-select')

<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $shippingZone?->name) }}" placeholder="e.g. Local Jaipur" maxlength="100" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code', $shippingZone?->code) }}" placeholder="e.g. LOCAL-JPR" maxlength="20" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Coverage Type <span class="text-danger">*</span></label>
                <select name="coverage_type" id="coverage_type" class="form-control" required>
                    @foreach(\App\Models\ShippingZone::COVERAGE_TYPES as $value => $label)
                        <option value="{{ $value }}" @selected(old('coverage_type', $shippingZone?->coverage_type ?? \App\Models\ShippingZone::COVERAGE_STATE) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Priority <span class="text-danger">*</span></label> <small>(lower number wins)</small>
                <input type="number" name="priority" class="form-control" value="{{ old('priority', $shippingZone?->priority ?? 1) }}" min="1" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="is_active" class="form-control" required>
                    <option value="1" @selected((string) old('is_active', (int) ($shippingZone?->is_active ?? true)) === '1')>Active</option>
                    <option value="0" @selected((string) old('is_active', (int) ($shippingZone?->is_active ?? true)) === '0')>Inactive</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row js-coverage" data-coverage="state">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">States <span class="text-danger">*</span></label>
                <select name="state_ids[]" class="form-control js-location-select" data-lookup-url="{{ route('admin.locations.lookup.states') }}" data-placeholder="Search and select states" multiple>
                    @foreach($selectedStates as $state)
                        <option value="{{ $state->id }}" selected>{{ $state->selectLabel() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row js-coverage" data-coverage="city">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">Cities <span class="text-danger">*</span></label>
                <select name="city_ids[]" class="form-control js-location-select" data-lookup-url="{{ route('admin.locations.lookup.cities') }}" data-placeholder="Search and select cities" multiple>
                    @foreach($selectedCities as $city)
                        <option value="{{ $city->id }}" selected>{{ $city->selectLabel() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row js-coverage" data-coverage="all">
        <div class="col-md-12">
            <div class="alert alert-info">This zone covers every location not matched by a higher-priority state or city zone.</div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">Description</label>
                <input type="text" name="description" class="form-control" value="{{ old('description', $shippingZone?->description) }}" maxlength="255">
            </div>
        </div>
    </div>
</div>

@include('admin.partials.location-select')

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const coverageType = document.getElementById('coverage_type');
            const coverageSections = document.querySelectorAll('.js-coverage');

            function toggleCoverageSections() {
                coverageSections.forEach((section) => {
                    section.style.display = section.dataset.coverage === coverageType.value ? '' : 'none';
                });
            }

            coverageType.addEventListener('change', toggleCoverageSections);
            toggleCoverageSections();
        });
    </script>
@endpush

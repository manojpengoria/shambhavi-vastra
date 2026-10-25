<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $courierPartner?->name) }}" placeholder="e.g. Delhivery" maxlength="100" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Code <span class="text-danger">*</span></label>
                <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code', $courierPartner?->code) }}" placeholder="e.g. DLV" maxlength="20" required>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Logo</label> <small>(JPG, PNG or WEBP, max 1 MB)</small>
                <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                @if($courierPartner?->logo)
                    <div class="margin-top-10">
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($courierPartner->logo) }}" alt="{{ $courierPartner->name }}" style="max-height: 40px; max-width: 120px;">
                        <label class="margin-left-10"><input type="checkbox" name="remove_logo" value="1" @checked(old('remove_logo'))> Remove logo</label>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Tracking URL</label> <small>(use {AWB} as placeholder)</small>
                <input type="text" name="tracking_url" class="form-control" value="{{ old('tracking_url', $courierPartner?->tracking_url) }}" placeholder="https://www.delhivery.com/track/package/{AWB}" maxlength="255">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Integration Type <span class="text-danger">*</span></label>
                <select name="integration_type" id="integration_type" class="form-control" required>
                    @foreach(\App\Models\CourierPartner::INTEGRATION_TYPES as $value => $label)
                        <option value="{{ $value }}" @selected(old('integration_type', $courierPartner?->integration_type ?? \App\Models\CourierPartner::TYPE_MANUAL) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-4 js-api-credentials">
            <div class="form-group">
                <label class="control-label">API Key</label>
                <input type="password" name="api_key" class="form-control" value="" autocomplete="new-password" placeholder="{{ $courierPartner?->api_key ? 'Saved — leave blank to keep' : 'Enter API key' }}" maxlength="500">
            </div>
        </div>
        <div class="col-md-4 js-api-credentials">
            <div class="form-group">
                <label class="control-label">API Secret</label>
                <input type="password" name="api_secret" class="form-control" value="" autocomplete="new-password" placeholder="{{ $courierPartner?->api_secret ? 'Saved — leave blank to keep' : 'Enter API secret' }}" maxlength="500">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Priority <span class="text-danger">*</span></label> <small>(1 = first choice)</small>
                <input type="number" name="priority" class="form-control" value="{{ old('priority', $courierPartner?->priority ?? 1) }}" min="1" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Max Weight (kg)</label>
                <input type="number" name="max_weight" class="form-control" value="{{ old('max_weight', $courierPartner?->max_weight) }}" min="0" step="0.001">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Max COD Amount (₹)</label>
                <input type="number" name="max_cod_amount" class="form-control" value="{{ old('max_cod_amount', $courierPartner?->max_cod_amount) }}" min="0" step="0.01">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label class="control-label">Capabilities</label>
                <div class="checkbox">
                    <input type="hidden" name="supports_cod" value="0">
                    <label><input type="checkbox" name="supports_cod" value="1" @checked(old('supports_cod', $courierPartner?->supports_cod ?? true))> Supports Cash on Delivery</label>
                </div>
                <div class="checkbox">
                    <input type="hidden" name="supports_reverse_pickup" value="0">
                    <label><input type="checkbox" name="supports_reverse_pickup" value="1" @checked(old('supports_reverse_pickup', $courierPartner?->supports_reverse_pickup ?? false))> Supports Reverse Pickup</label>
                </div>
                <div class="checkbox">
                    <input type="hidden" name="supports_express" value="0">
                    <label><input type="checkbox" name="supports_express" value="1" @checked(old('supports_express', $courierPartner?->supports_express ?? false))> Supports Express Delivery</label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="is_active" class="form-control" required>
                    <option value="1" @selected((string) old('is_active', (int) ($courierPartner?->is_active ?? true)) === '1')>Active</option>
                    <option value="0" @selected((string) old('is_active', (int) ($courierPartner?->is_active ?? true)) === '0')>Inactive</option>
                </select>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const integrationType = document.getElementById('integration_type');
            const credentialFields = document.querySelectorAll('.js-api-credentials');

            function toggleCredentialFields() {
                credentialFields.forEach((field) => {
                    field.style.display = integrationType.value === 'api' ? '' : 'none';
                });
            }

            integrationType.addEventListener('change', toggleCredentialFields);
            toggleCredentialFields();
        });
    </script>
@endpush

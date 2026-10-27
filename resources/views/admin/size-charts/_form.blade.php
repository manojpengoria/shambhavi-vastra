@php
    $chartColumns = array_values((array) old('columns', $sizeChart?->columns ?? ['Size', 'Bust', 'Waist', 'Hip', 'Length']));
    $defaultRows = array_map(
        fn (string $size) => array_pad([$size], count($chartColumns), null),
        ($defaultSizes ?? []) ?: ['S', 'M', 'L', 'XL']
    );
    $chartRows = array_values((array) old('rows', $sizeChart?->rows ?? $defaultRows));
@endphp
<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group @error('name') has-error @enderror">
                <label class="control-label">Size Chart Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $sizeChart?->name) }}" placeholder="e.g. Women Kurti" maxlength="100" required>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div class="form-group">
                <label class="control-label">Unit <span class="text-danger">*</span></label>
                <select name="unit" class="form-control" required>
                    @foreach (\App\Models\SizeChart::UNITS as $value => $label)
                        <option value="{{ $value }}" @selected(old('unit', $sizeChart?->unit ?? 'inch') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div class="form-group">
                <label class="control-label">Status</label>
                <select name="status" class="form-control" required>
                    <option value="707" @selected((string) old('status', $sizeChart?->status ?? 707) === '707')>Active</option>
                    <option value="505" @selected((string) old('status', $sizeChart?->status ?? 707) === '505')>Inactive</option>
                </select>
            </div>
        </div>
        <div class="col-md-2 col-sm-4">
            <div class="form-group">
                <label class="control-label">Sort Order</label>
                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $sizeChart?->sort_order ?? 0) }}" min="0" required>
            </div>
        </div>
    </div>

    <div class="form-group @error('columns') has-error @enderror @error('rows') has-error @enderror">
        <label class="control-label">Measurements <span class="text-danger">*</span></label>
        <p class="help-block">The first column holds the size (S, M, L...). Rename columns, add rows or columns as needed; empty rows are ignored.</p>
        <div class="table-responsive">
            <table class="table table-bordered size-chart-editor" id="size-chart-editor" data-max-columns="{{ \App\Models\SizeChart::MAX_COLUMNS }}" data-max-rows="{{ \App\Models\SizeChart::MAX_ROWS }}">
                <thead>
                    <tr>
                        @foreach ($chartColumns as $columnIndex => $column)
                            <th>
                                <div class="size-chart-column">
                                    <input type="text" name="columns[]" class="form-control input-sm" value="{{ $column }}" maxlength="30" aria-label="Column {{ $columnIndex + 1 }} heading" required>
                                    @if ($columnIndex > 0)
                                        <button type="button" class="btn btn-xs red btn-outline" data-remove-column title="Remove column"><i class="fa fa-times"></i></button>
                                    @endif
                                </div>
                            </th>
                        @endforeach
                        <th class="size-chart-actions" aria-label="Row actions"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chartRows as $rowIndex => $row)
                        <tr>
                            @foreach ($chartColumns as $columnIndex => $column)
                                <td>
                                    <input type="text" name="rows[{{ $rowIndex }}][{{ $columnIndex }}]" class="form-control input-sm" value="{{ $row[$columnIndex] ?? '' }}" maxlength="30" aria-label="Row {{ $rowIndex + 1 }}, {{ $column }}">
                                </td>
                            @endforeach
                            <td class="size-chart-actions">
                                <button type="button" class="btn btn-xs red btn-outline" data-remove-row title="Remove row"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="button" class="btn btn-sm blue btn-outline" id="size-chart-add-row"><i class="fa fa-plus"></i> Add Row</button>
        <button type="button" class="btn btn-sm blue btn-outline" id="size-chart-add-column"><i class="fa fa-plus"></i> Add Column</button>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <label class="control-label">How to Measure</label>
                <textarea name="how_to_measure" class="form-control" rows="4" maxlength="2000" placeholder="e.g. Bust: measure around the fullest part of the chest.">{{ old('how_to_measure', $sizeChart?->how_to_measure) }}</textarea>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group @error('image') has-error @enderror">
                <label class="control-label">Measuring Guide Image</label> <small>(optional, JPG/PNG/WEBP, max 2 MB)</small>
                <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                @if ($sizeChart?->image)
                    <div class="margin-top-10">
                        <img src="{{ $sizeChart->imageUrl() }}" alt="{{ $sizeChart->name }} guide" style="max-height: 90px; max-width: 160px; border: 1px solid #eee;">
                        <label class="margin-left-10"><input type="checkbox" name="remove_image" value="1"> Remove image</label>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .size-chart-editor {
            margin-bottom: 10px;
        }

        .size-chart-editor th,
        .size-chart-editor td {
            min-width: 110px;
            vertical-align: middle !important;
        }

        .size-chart-editor .size-chart-actions {
            width: 50px;
            min-width: 50px;
            text-align: center;
        }

        .size-chart-column {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .size-chart-editor thead .form-control {
            font-weight: 600;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('size-chart-editor');
            const headRow = table.tHead.rows[0];
            const body = table.tBodies[0];
            const maxColumns = Number(table.dataset.maxColumns);
            const maxRows = Number(table.dataset.maxRows);
            const addRowButton = document.getElementById('size-chart-add-row');
            const addColumnButton = document.getElementById('size-chart-add-column');

            const columnCount = function () {
                return headRow.cells.length - 1;
            };

            const cellInput = function (name) {
                const input = document.createElement('input');
                input.type = 'text';
                input.name = name;
                input.className = 'form-control input-sm';
                input.maxLength = 30;

                return input;
            };

            // Keep rows[row][column] names in order after adding/removing rows or columns.
            const renumber = function () {
                Array.from(body.rows).forEach(function (row, rowIndex) {
                    Array.from(row.cells).slice(0, -1).forEach(function (cell, columnIndex) {
                        cell.querySelector('input').name = 'rows[' + rowIndex + '][' + columnIndex + ']';
                    });
                });

                addRowButton.disabled = body.rows.length >= maxRows;
                addColumnButton.disabled = columnCount() >= maxColumns;
            };

            const addRow = function () {
                const row = body.insertRow();

                for (let index = 0; index < columnCount(); index++) {
                    row.insertCell().appendChild(cellInput(''));
                }

                const actions = row.insertCell();
                actions.className = 'size-chart-actions';
                actions.innerHTML = '<button type="button" class="btn btn-xs red btn-outline" data-remove-row title="Remove row"><i class="fa fa-trash"></i></button>';
                renumber();
                row.cells[0].querySelector('input').focus();
            };

            const addColumn = function () {
                const header = document.createElement('th');
                header.innerHTML = '<div class="size-chart-column"><button type="button" class="btn btn-xs red btn-outline" data-remove-column title="Remove column"><i class="fa fa-times"></i></button></div>';
                const headingInput = cellInput('columns[]');
                headingInput.required = true;
                headingInput.placeholder = 'Heading';
                header.firstChild.prepend(headingInput);
                headRow.insertBefore(header, headRow.lastElementChild);

                Array.from(body.rows).forEach(function (row) {
                    const cell = document.createElement('td');
                    cell.appendChild(cellInput(''));
                    row.insertBefore(cell, row.lastElementChild);
                });

                renumber();
                headingInput.focus();
            };

            table.addEventListener('click', function (event) {
                const removeRow = event.target.closest('[data-remove-row]');
                const removeColumn = event.target.closest('[data-remove-column]');

                if (removeRow) {
                    if (body.rows.length > 1) {
                        removeRow.closest('tr').remove();
                        renumber();
                    }

                    return;
                }

                if (removeColumn) {
                    const columnIndex = Array.from(headRow.cells).indexOf(removeColumn.closest('th'));
                    headRow.deleteCell(columnIndex);
                    Array.from(body.rows).forEach(function (row) {
                        row.deleteCell(columnIndex);
                    });
                    renumber();
                }
            });

            addRowButton.addEventListener('click', addRow);
            addColumnButton.addEventListener('click', addColumn);
            renumber();
        });
    </script>
@endpush

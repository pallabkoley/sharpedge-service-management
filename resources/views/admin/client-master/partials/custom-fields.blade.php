<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-tags me-2"></i>Custom Fields</h6>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#customFieldModal">
            <i class="bi bi-plus-circle me-1"></i> Add Custom Field
        </button>
    </div>
    <div class="card-body">
        @if($customFields->count() > 0)
            <form action="{{ route('admin.client-master.custom-fields.values.save', $client) }}" method="POST">
                @csrf
                <div class="row">
                    @foreach($customFields as $field)
                        @php
                            $value = $client->customFieldValues->where('custom_field_id', $field->id)->first();
                            $fieldValue = $value ? $value->field_value : null;
                        @endphp
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">
                                {{ $field->field_label }}
                                @if($field->is_required) <span class="text-danger">*</span> @endif
                            </label>
                            @switch($field->field_type)
                                @case('text')
                                    <input type="text" name="fields[{{ $field->id }}]" class="form-control" value="{{ $fieldValue }}" @if($field->is_required) required @endif>
                                    @break
                                @case('number')
                                    <input type="number" name="fields[{{ $field->id }}]" class="form-control" value="{{ $fieldValue }}" @if($field->is_required) required @endif>
                                    @break
                                @case('email')
                                    <input type="email" name="fields[{{ $field->id }}]" class="form-control" value="{{ $fieldValue }}" @if($field->is_required) required @endif>
                                    @break
                                @case('date')
                                    <input type="date" name="fields[{{ $field->id }}]" class="form-control" value="{{ $fieldValue }}" @if($field->is_required) required @endif>
                                    @break
                                @case('textarea')
                                    <textarea name="fields[{{ $field->id }}]" class="form-control" rows="2" @if($field->is_required) required @endif>{{ $fieldValue }}</textarea>
                                    @break
                                @case('select')
                                    @php $options = explode(',', $field->options ?? ''); @endphp
                                    <select name="fields[{{ $field->id }}]" class="form-select" @if($field->is_required) required @endif>
                                        <option value="">Select</option>
                                        @foreach($options as $opt)
                                            <option value="{{ trim($opt) }}" {{ trim($opt) == $fieldValue ? 'selected' : '' }}>{{ trim($opt) }}</option>
                                        @endforeach
                                    </select>
                                    @break
                                @case('checkbox')
                                    <div class="form-check">
                                        <input type="hidden" name="fields[{{ $field->id }}]" value="0">
                                        <input type="checkbox" name="fields[{{ $field->id }}]" class="form-check-input" value="1" {{ $fieldValue == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label">Enabled</label>
                                    </div>
                                    @break
                                @default
                                    <input type="text" name="fields[{{ $field->id }}]" class="form-control" value="{{ $fieldValue }}">
                            @endswitch
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Save Custom Field Values</button>
            </form>
        @else
            <p class="text-muted text-center py-3">No custom fields defined. Click "Add Custom Field" to create one.</p>
        @endif
    </div>
</div>
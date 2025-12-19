@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'required' => false])
<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    @endif
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name, $value) }}"
           {{ $attributes->merge(['class' => 'form-control'.($errors->has($name) ? ' is-invalid' : '')]) }}
           @if($required) required @endif>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

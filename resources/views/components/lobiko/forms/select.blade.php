@props(['name', 'label' => null, 'options' => [], 'value' => null, 'required' => false, 'placeholder' => null])
<div class="mb-3">
    @if($label)
        <label for="{{ $name }}" class="form-label">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
    @endif
    <select name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'form-select'.($errors->has($name) ? ' is-invalid' : '')]) }} @if($required) required @endif>
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach($options as $key => $text)
            <option value="{{ $key }}" @selected(old($name, $value) == $key)>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

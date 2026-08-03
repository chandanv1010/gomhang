@props(['heading' => '', 'name', 'options', 'selectedValue' => [], 'multiple' => false])
<div {{ $attributes->merge(['class' => 'row']) }}>
    <div class="col-lg-12">
        <div class="form-row">
            {!! !empty($heading) ? '<span class="text-danger notice">' . $heading . '</span>' : '' !!}
            <select name="{{ $name }}{{ $multiple ? '[]' : '' }}" 
                    class="form-control setupSelect2" 
                    id="{{ $name }}-select-2-{{ time() }}"
                    {{ $multiple ? 'multiple' : '' }}>
                @foreach($options as $key => $val)
                    <option value="{{ $key }}" 
                        {{ in_array($key, (array) old($name, $selectedValue)) ? 'selected' : '' }}>
                        {{ $val }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>
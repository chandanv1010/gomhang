@props(['name', 'value'])
<div class="row">
    <div class="col-lg-12">
        <div class="form-row">
            <span class="image img-cover image-target"><img src="{{ (old($name, ($value) ?? '' ) ? old($name, ($value) ?? '')   :  'backend/img/not-found.jpg') }}" alt=""></span>
            <input type="hidden" name="{{ $name }}" value="{{ old($name, ($value) ?? '' ) }}">
        </div>
    </div>
</div>
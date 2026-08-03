<div class="ibox">
    <div class="ibox-title">
        <div>
            <h5>{{ __('messages.add_scholar_policy') }}</h5>
        </div>
    </div>
    <div class="ibox-content">
        <div class="variant-foot mt10">
            <button type="button" class="add-policy">{{ __('messages.add_policy') }}</button>
        </div>
        <div class="program-content mt20">
            @php
                $scholar_policies = old('scholar_policy', !is_null($model) ? $model->scholar_policy : []);
            @endphp
            @if(isset($scholar_policies) && is_array($scholar_policies) && count($scholar_policies))
                @foreach($scholar_policies as $index => $scholar_policy)
                    <div class="ibox mt20 policy-wrapper" data-policy-index="{{ $index }}">
                        <div class="ibox-title">
                            <div class="uk-flex uk-flex-middle uk-flex-space-between mb15">
                                <input type="text" 
                                    name="scholar_policy[{{ $index }}][title]" 
                                    class="form-control" 
                                    value="{{ $scholar_policy['title'] ?? '' }}" 
                                    placeholder="{{ __('messages.enter_main_name') }}" style="width:75%;">
                                <div class="chapter-action">
                                    <button type="button" class="remove-policy-item">{{ __('messages.delete_policy') }}</button>
                                </div>
                            </div>
                            <div class="form-row">
                                <label for="" class="control-label text-left">{{ __('messages.content') }}</label>
                                <textarea name="scholar_policy[{{ $index }}][description]" class="ck-editor" id="scholar_policy[{{ $index }}]" placeholder="{{ __('messages.enter_desc_name') }}" style="width:100%; margin-top:10px;">{{ $scholar_policy['description'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
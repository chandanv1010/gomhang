<div class="uk-overflow-container">
    <table class="uk-table uk-table-striped uk-table-middle compare-table">
        <thead>
            <tr>
                <th class="compare-title-col">Sản phẩm</th>
                @foreach ($compareSlots as $slot)
                    <th class="compare-slot" data-position="{{ $slot['position'] }}">
                        @if ($slot['product'])
                            <div class="compare-slot-header uk-flex uk-flex-column uk-flex-center">
                                <button type="button" class="compare-remove uk-position-absolute"
                                    data-rowid="{{ $slot['rowId'] }}" data-position="{{ $slot['position'] }}"
                                    aria-label="Bỏ sản phẩm">
                                    <i class="fa fa-times"></i>
                                </button>
                                <div class="compare-slot-thumb image img-scaledown uk-margin-small-bottom">
                                    <img src="{{ $slot['product']['image'] }}" alt="{{ $slot['product']['name'] }}">
                                </div>
                                <div class="compare-slot-meta uk-width-1-1">
                                    <a href="{{ $slot['product']['canonical'] }}"
                                        class="compare-slot-name uk-display-block uk-text-center" target="_blank"
                                        rel="noopener">
                                        {{ $slot['product']['name'] }}
                                    </a>
                                    <div class="compare-slot-price uk-flex uk-flex-center uk-width-1-1">
                                        {!! $slot['product']['price_html'] !!}
                                    </div>
                                </div>
                            </div>
                        @else
                            <button type="button"
                                class="compare-slot-empty open-compare-modal uk-flex uk-flex-column uk-flex-center uk-flex-middle"
                                data-position="{{ $slot['position'] }}">
                                <span class="compare-plus uk-display-inline-flex uk-flex-center uk-flex-middle">+</span>
                                <span>Thêm sản phẩm</span>
                            </button>
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($compareFields as $field)
                <tr>
                    <th class="uk-text-left">{{ $field['label'] }}</th>
                    @foreach ($compareSlots as $slot)
                        <td class="uk-text-center">
                            @if ($slot['product'] && isset($slot['product']['fields'][$field['key']]))
                                <div class="compare-field-content">
                                    @if (($field['type'] ?? 'text') === 'html')
                                        {!! $slot['product']['fields'][$field['key']] !!}
                                    @else
                                        {{ $slot['product']['fields'][$field['key']] }}
                                    @endif
                                </div>
                            @else
                                <span class="compare-placeholder">—</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


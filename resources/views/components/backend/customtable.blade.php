@props(['columns', 'records', 'actions' => [], 'model'])

<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th style="width:30px;">
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        @foreach($columns as $col)
        <th class="{{ isset($col['class']) ? $col['class'] : '' }}">{{ $col['label'] }}</th>
        @endforeach
        <th style="width:100px;" class="text-center">Sắp xếp</th>
        <th style="width:100px;" class="text-center">Tình Trạng</th>
        <th style="width:100px;" class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
        @forelse($records as $record)
            <tr>
                <td>
                    <input type="checkbox" 
                        value="{{ $record->id }}" 
                        class="input-checkbox checkBoxItem">
                </td>

                @foreach($columns as $col)
                    <td class="{{ isset($col['class']) ? $col['class'] : '' }}">
                        {!! $col['render']($record) !!}
                    </td>
                @endforeach

                <td>
                    <input type="text" 
                        name="order" 
                        value="{{ $record->order ?? 0 }}" 
                        class="form-control change-order text-right" 
                        data-model="{{ $model }}"
                        data-id="{{ $record->id }}"
                    >
                </td>
                <td class="text-center js-switch-{{ $record->id }}">
                    <input type="checkbox" 
                        class="js-switch change-status" 
                        value="{{ $record->publish }}" 
                        data-field="publish" 
                        data-model="{{ $model }}"
                        data-id="{{ $record->id }}"
                        {{ $record->publish == 2 ? 'checked' : '' }}>
                </td>
                <td class="text-center">
                    @foreach($actions as $action)
                        <a href="{{ route($action['route'], $record->id) }}" 
                        class="btn {{ $action['class'] }} btn-sm me-1">
                            <i class="fa {{ $action['icon'] }}"></i>
                        </a>
                    @endforeach
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-sm text-danger">Không có dữ liệu phù hợp</td>
            </tr>
        @endforelse
    </tbody>
</table>

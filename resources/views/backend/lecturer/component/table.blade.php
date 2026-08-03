<table class="table table-striped table-bordered">
    <thead>
    <tr>
        <th>
            <input type="checkbox" value="" id="checkAll" class="input-checkbox">
        </th>
        <th>Thông tin</th>
        <th>Chức vụ</th>
        <th>Mô tả</th>
        <th class="text-center">Tình Trạng</th>
        <th class="text-center">Thao tác</th>
    </tr>
    </thead>
    <tbody>
        @if(isset($lecturers) && is_object($lecturers))
            @foreach($lecturers as $lecturer)
            <tr >
                <td>
                    <input type="checkbox" value="{{ $lecturer->id }}" class="input-checkbox checkBoxItem">
                </td>
                <td>
                    <img src="{{ $lecturer->image }}" alt="" style="width:60px;height:60px; margin-right:10px;">
                    <span class="text-success">{{ $lecturer->name }}</span>
                </td>
                <td>
                    {{ $lecturer->position }}
                </td>
                <td style="width:300px;">
                    {{ $lecturer->description }}
                </td>
                <td class="text-center js-switch-{{ $lecturer->id }}"> 
                    <input type="checkbox" value="{{ $lecturer->publish }}" class="js-switch status " data-field="publish" data-model="{{ $config['model'] }}" {{ ($lecturer->publish == 2) ? 'checked' : '' }} data-modelId="{{ $lecturer->id }}" />
                </td>
                <td class="text-center"> 
                    <a href="{{ route('lecturer.edit', $lecturer->id) }}" class="btn btn-success"><i class="fa fa-edit"></i></a>
                    <a href="{{ route('lecturer.delete', $lecturer->id) }}" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{  $lecturers->links('pagination::bootstrap-4') }}

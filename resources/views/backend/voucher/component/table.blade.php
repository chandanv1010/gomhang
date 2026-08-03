<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>
                <input type="checkbox" value="" id="checkAll" class="input-checkbox">
            </th>
            <th>Tên Voucher</th>
            <th>Chiết khấu</th>
            <th>Ngày bắt đầu</th>
            <th>Ngày kết thúc</th>
            <th>Số lượng</th>
            <th class="text-center">Thao tác</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($vouchers) && is_object($vouchers))
            @foreach($vouchers as $key => $voucher)
            @php
                $start_date = convertDateTime($voucher->start_date, 'H:i d/m/Y');
                $end_date = convertDateTime($voucher->end_date, 'H:i d/m/Y');
                $status = '';
                if($voucher->end_date != NULL && strtotime($voucher->end_date) - strtotime(now()) <= 0){
                    $status = '<span class="text-danger text-small">- Hết Hạn</span>';
                }
            @endphp
            <tr >
                <td>
                    <input type="checkbox" value="{{ $voucher->id }}" class="input-checkbox checkBoxItem">
                </td>
                <td>
                    <div>{{ $voucher->name }} {!! $status !!} </div> 
                    <div class="text-small text-success">Mã : {{ $voucher->code }}</div> 
                    <div class="text-small text-success">Loại Voucher : {{ __('module.voucher')[$voucher->method] }}</div>
                </td>
                <th>
                    <div class="voucher-information">
                        {!! renderDiscountVoucher($voucher);  !!}
                    </div>
                </th>
                <td>
                    {{ $start_date }}
                </td>
                <td>
                    {{ $end_date }}
                </td>
                <th>
                    {{ $voucher->total_quantity }}
                </th>
                <td class="text-center"> 
                    <a href="{{ route('voucher.edit', $voucher->id) }}" class="btn btn-success {{ $voucher->used_quantity !== 0 ? 'disabled' : '' }}"><i class="fa fa-edit"></i></a>
                    <a href="{{ route('voucher.delete', $voucher->id) }}" class="btn btn-danger {{ $voucher->used_quantity !== 0  ? 'disabled' : '' }}"><i class="fa fa-trash"></i></a>
                </td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>
{{  $vouchers->links('pagination::bootstrap-4') }}

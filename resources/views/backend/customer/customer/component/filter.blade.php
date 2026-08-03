<form action="{{ route('customer.index') }}">
    <div class="filter-wrapper">
        <div class="uk-flex uk-flex-middle uk-flex-space-between">
            <div class="perpage">
                @php
                    $perpage = request('perpage') ?: old('perpage');
                @endphp
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <select name="perpage" class="form-control input-sm perpage filter mr10">
                        @for($i = 20; $i<= 200; $i+=20)
                        <option {{ ($perpage == $i)  ? 'selected' : '' }}  value="{{ $i }}">{{ $i }} bản ghi</option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="action">
                <div class="uk-flex uk-flex-middle">
                    <button data-toggle="modal" data-target="#exportExcelBtn" type="button" name="" class="btn btn-primary mr10">
                        <i class="fa fa-file-excel-o mr5"></i>Xuất Excel
                    </button>
                    @include('backend.dashboard.component.filterPublish')
                    <select name="customer_catalogue_id" class="form-control mr10 setupSelect2">
                        <option value="0" selected="selected">Chọn Nhóm Thành Viên</option>
                        @foreach($customerCatalogues as $key => $val)
                        <option value="{{ $val->id }}">{{ $val->name }}</option>
                        @endforeach
                    </select>
                    @include('backend.dashboard.component.keyword')
                    <a href="{{ route('customer.create') }}" class="btn btn-danger"><i class="fa fa-plus mr5"></i>Thêm mới thành viên</a>
                </div>
            </div>
        </div>
    </div>
</form>
<div id="exportExcelBtn" class="modal fade">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Dữ liệu khách hàng</h3>
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
        </div>
        <div class="date-inputs">
            <div class="date-input-group">
                <label for="startDate">Từ ngày</label>
                <input type="date" name="startDate" id="startDate" required>
            </div>
            <div class="date-input-group">
                <label for="endDate">Đến ngày</label>
                <input type="date" name="endDate" id="endDate" required>
            </div>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn btn-white" data-dismiss="modal">Hủy</button>
            <button class="btn btn-excel" id="confirmExport" data-model="Customer">
                <i class="fa fa-download"></i> Xuất Excel
            </button>
        </div>
    </div>
</div>
<style>
    .modal-content {
        background-color: white;
        padding: 25px;
        border-radius: 10px;
        width: 400px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        margin-left: auto;
        margin-right: auto;
        margin-top: 200px;
    }
        
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0 !important;
        margin-bottom: 15px;
    }
    
    .modal-header h3 {
        color: #2c3e50;
        width: 100%;
    }
     .close {
        font-size: 24px;
        cursor: pointer;
        color: #777;
    }
        
    .date-inputs {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .date-input-group {
        display: flex;
        flex-direction: column;
    }
    
    .date-input-group label {
        margin-bottom: 5px;
        font-weight: 500;
    }
    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn-excel {
        background-color: #1d6f42;
        color: white;
    }
    .btn-excel:hover {
        background-color: #166534;
    }
    .date-input-group input{
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }
    #confirmExport{
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
</style>
<script>
    document.getElementById('startDate').valueAsDate = new Date();
    document.getElementById('endDate').valueAsDate = new Date();
</script>
<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exports\CustomerExport;
use App\Exports\OrderExport;

class ExcelController extends Controller
{

    public function __construct(
    
    ){
        
    }

    public function export(Request $request){
        $model = $request->input('model');
        $class = loadClass($model);
        if($model == 'Customer'){
            $customers = $class->getCustomerByDate($request);
            $export = new CustomerExport($customers, $request);
            $temp_file = $export->export();
            $filename = "Danh_sach_khach_hang";
        }else{
            $orders = $class->getOrderByDate($request);
            $export = new OrderExport($orders, $request);
            $temp_file = $export->export();
            $filename = "Danh_sach_don_hang";
        }
        return response()->json([
            'status' => 'success',
            'file_url' => url('temp/' . basename($temp_file)), // URL của file tạm thời
            'filename' => $filename,
        ]);
    }

}

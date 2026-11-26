<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Dia gioi sau sap nhap (01/07/2025) chi con 2 cap: Tinh/Thanh pho -> Phuong/Xa.
     * location_id la ma tinh; tra ve danh sach phuong/xa cua tinh do.
     * (target 'districts' cu cung duoc tra ve phuong/xa de form cu khong bi treo.)
     */
    public function getLocation(Request $request){
        $provinceCode = (string) data_get($request->input(), 'data.location_id', '');

        $wards = $provinceCode === ''
            ? collect()
            : Ward::where('province_code', $provinceCode)->orderBy('name')->get(['code', 'name']);

        return response()->json([
            'html' => $this->renderHtml($wards),
        ]);
    }

    public function renderHtml($wards, $root = '[Chọn Phường/Xã]'){
        $html = '<option value="">'.$root.'</option>';
        foreach($wards as $ward){
            $html .= '<option value="'.e($ward->code).'">'.e($ward->name).'</option>';
        }
        return $html;
    }
}

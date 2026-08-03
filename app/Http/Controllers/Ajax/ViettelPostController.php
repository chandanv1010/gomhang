<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Classes\ViettelPost;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FrontendController;
use App\Models\System;

class ViettelPostController extends FrontendController
{

    // private $token;
  

    public function __construct(
        
    ){
        parent::__construct();
    }

   

    public function getDistrict(Request $request){

        // dd($this->system);

        $buyer = Auth::guard('customer')->user();
        $get = $request->input();
        $provinceId = $get['city_id'];
        $viettelPost = new ViettelPost(
            $this->system['homepage_viettelpost_email'],
            $this->system['homepage_viettelpost_password']
        );
        $token = $viettelPost->getToken();
        $districts = $viettelPost->getDistricts($token, $provinceId); 
        return response()->json([
            'html' => $this->renderDistrictHtml($districts),
        ]);

    }

    public function getWard(Request $request){

        $buyer = Auth::guard('customer')->user();
        $get = $request->input();
        $districtId = $get['district_id'];

        $viettelPost = new ViettelPost(
            $this->system['homepage_viettelpost_email'],
            $this->system['homepage_viettelpost_password']
        );
        $token = $viettelPost->getToken();
        $wards = $viettelPost->getWards($token, $districtId); 
        return response()->json([
            'html' => $this->renderHtml($wards),
        ]);
    }

    public function renderDistrictHtml($districts,  $root = '[Chọn Quận/Huyện]'){
        $html = '<option value="0" selected>'.$root.'</option>';
        foreach($districts['data'] as $district){
            $html .= '<option value="'.$district['DISTRICT_ID'].'">'.$district['DISTRICT_NAME'].'</option>';
        }
        return $html;
    }

    public function renderHtml($wards, $root = '[Chọn Quận/Huyện]'){
        $html = '<option value="0">'.$root.'</option>';
        foreach($wards['data'] as $ward){
            $html .= '<option value="'.$ward['WARDS_ID'].'">'.$ward['WARDS_NAME'].'</option>';
        }
        return $html;
    }
}

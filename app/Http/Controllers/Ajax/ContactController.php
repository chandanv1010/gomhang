<?php

namespace App\Http\Controllers\Ajax;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Services\V1\Core\ContactService;
use Illuminate\Support\Facades\DB;
use App\Models\Scholar;

class ContactController extends Controller
{

    protected $contactService;
    
    public function __construct(
        ContactService $contactService
    ){
        $this->contactService = $contactService;
    }

    public function requestConsult(Request $request){
        $flag = $this->contactService->create($request);
        return response()->json([
            'status' => $flag['code'] == 10 ? true : false,
            'messages' => 'Gửi yêu cầu thành công , chúng tôi sẽ sớm liên hệ với bạn',
        ]);
    }

    public function quickConsult(Request $request){
        $flag = $this->contactService->create($request);
        return response()->json([
            'status' => $flag['code'] == 10 ? true : false,
            'messages' => 'Gửi yêu cầu thành công , chúng tôi sẽ sớm liên hệ với bạn',
        ]);
    }

    public function advise(Request $request){
        $rules = [
            'name' => 'required',
            'phone' => 'required',
        ];
        
        $errorMessages = [
            'name.required' => 'Bạn chưa nhập họ tên.',
            'phone.required' => 'Bạn chưa nhập số điện thoại.',
        ];

        $validator = Validator::make($request->all(), $rules, $errorMessages);

        if($validator->fails()) {
            $errors = $validator->errors();
            $response = [
                'status' => 422,
                'messages' => [
                    'name' => $errors->first('name'),
                    'phone' => $errors->first('phone'),
                ],
            ];
        
            return response()->json($response);
        }

        $flag = $this->contactService->create($request);

        return response()->json([
            'response' => $flag, 
            'messages' => 'Đặt hàng thành công',
            'code' => (!$flag) ? 11 : 10,
        ]);  
    }

    public function create(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|regex:/^0[0-9]{9}$/',
            'scholarshipType' => 'required|integer|min:1',
            'address' => 'required|string|max:500',
        ]);

        $scholar = Scholar::with(['languages'])->find($validated['scholarshipType']);

        try {
            DB::beginTransaction();
            DB::table('contacts')->insert([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'message' => '<div>
                    <h1>Đăng ký nhận thông tin chương trình ưu đãi</h1>
                    <div>Loại học bổng: '.$scholar->languages->first()->pivot->name.'</div>
                </div>',
                'created_at' => now(),
                'updated_at' => now() 
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Xử lý yêu cầu thành công',
                'code' => '200'
            ]);


        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return response()->json([
                'message' => 'Có vấn đề xảy ra trong quá trình xử lý',
                'code' => '500'
            ]);
        }

    }

     public function createScholar(Request $request){
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|regex:/^0[0-9]{9}$/',
            'destination_area' => 'required|',
            'apply_for' => 'required',
        ]);

        // dd(12312423);

        // $scholar = Scholar::with(['languages'])->find($validated['scholarshipType']);

        try {
            DB::beginTransaction();
            DB::table('contacts')->insert([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'] ?? '',
                'message' => '<div>
                    <h1>Đăng ký nhận tư vấn học bổng</h1>
                    <div>Loại học bổng: '.$validated['apply_for'].'</div>
                    <div>Khu vực: '.$validated['destination_area'].'</div>
                </div>',
                'created_at' => now(),
                'updated_at' => now() 
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Xử lý yêu cầu thành công',
                'code' => '200'
            ]);


        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th->getMessage());
            return response()->json([
                'message' => 'Có vấn đề xảy ra trong quá trình xử lý',
                'code' => '500'
            ]);
        }

    }

    public function buyNow(Request $request){
         $validated = $request->validate([
                'order_name'        => 'required|string|max:255',
                'order_email'       => 'required|email',
                'order_phone'       => 'required|regex:/^0[0-9]{9}$/',
                'order_address'     => 'required|string|max:255',
                'order_title_prd'   => 'required|string|max:255',
                'order_message'     => 'nullable|string',
            ]);
        try {
            DB::beginTransaction();

            DB::table('contacts')->insert([
                'name'       => $validated['order_name'],
                'email'      => $validated['order_email'],
                'phone'      => $validated['order_phone'],
                'address'    => $validated['order_address'],
                'message'    => '
                    <div>
                        <h1>Đặt mua sản phẩm</h1>
                        <div>Sản phẩm: ' . $validated['order_title_prd'] . '</div>
                        <div>Lời nhắn: ' . ($validated['order_message'] ?? '') . '</div>
                    </div>',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Xử lý yêu cầu thành công',
                'code'    => 200
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'message' => 'Có vấn đề xảy ra trong quá trình xử lý: ' . $th->getMessage(),
                'code'    => 500
            ]);
        }

    }

    
}

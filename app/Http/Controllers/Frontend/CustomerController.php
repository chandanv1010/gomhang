<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\FrontendController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Customer\EditProfileRequest;
use App\Http\Requests\Customer\RecoverCustomerPasswordRequest;
use Illuminate\Support\Facades\Hash;
use App\Services\V1\Customer\CustomerService;
use App\Models\Order;
use App\Models\CustomerPointHistory;

class CustomerController extends FrontendController
{

    protected $customerService;
    protected $constructRepository;
    protected $constructService;
    protected $customer;

    public function __construct(
        CustomerService $customerService,

    ) {

        $this->customerService = $customerService;

        parent::__construct();
    }


    public function profile()
    {

        $customer = Auth::guard('customer')->user();

        $system = $this->system;
        $seo = [
            'meta_title' => 'Trang quản lý tài khoản khách hàng' . $customer['name'],
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => route('customer.account')
        ];
        return view('frontend.auth.customer.profile', compact(
            'seo',
            'system',
            'customer',
        ));
    }

    public function updateProfile(EditProfileRequest $request)
    {
        $customerId =  Auth::guard('customer')->user()->id;
        if ($this->customerService->update($customerId, $request)) {
            return redirect()->route('customer.account')->with('success', 'Cập nhật bản ghi thành công');
        }
        return redirect()->route('customer.account')->with('error', 'Cập nhật bản ghi không thành công. Hãy thử lại');
    }

    public function changePassword()
    {

        $customer = Auth::guard('customer')->user();
        $system = $this->system;
        $seo = [
            'meta_title' => 'Trang thay đổi mật khẩu' . $customer['name'],
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => route('customer.password')
        ];
        return view('frontend.auth.customer.password', compact(
            'seo',
            'system',
            'customer',
        ));
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();

        $file = $request->file('avatar');
        $path = 'uploads/customer/avatar/';
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path($path), $filename);

        if ($customer->image && file_exists(public_path($customer->image))) {
            @unlink(public_path($customer->image));
        }

        $customer->image = $path . $filename;
        $customer->save();

        return response()->json([
            'status' => 'success',
            'url' => asset($customer->image)
        ]);
    }

    public function updatePassword(RecoverCustomerPasswordRequest $request)
    {

        /** @var \App\Models\Customer $customer */
        $customer = Auth::guard('customer')->user();

        if (!Hash::check($request->current_password, $customer->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng.');
        }
        // Thay đổi mật khẩu
        $customer->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Thay đổi mật khẩu thành công!');
    }

    public function historyOrder()
    {
        $customer = Auth::guard('customer')->user();

        $orders = Order::select([
            'orders.*',
            'provinces.name as province_name',
            'districts.name as district_name',
            'wards.name as ward_name',
        ])
            ->where('orders.customer_id', $customer->id)
            ->leftJoin('provinces', 'orders.province_id', '=', 'provinces.code')
            ->leftJoin('districts', 'orders.district_id', '=', 'districts.code')
            ->leftJoin('wards', 'orders.ward_id', '=', 'wards.code')
            ->orderBy('orders.created_at', 'desc')
            ->paginate(10);

        $system = $this->system;
        $seo = [
            'meta_title' => 'Lịch sử đơn hàng - ' . $customer->name,
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => route('customer.order')
        ];

        return view('frontend.auth.customer.order', compact(
            'seo',
            'system',
            'customer',
            'orders',
        ));
    }

    public function orderDetail($code)
    {
        $customer = Auth::guard('customer')->user();

        $order = Order::select([
            'orders.*',
            'provinces.name as province_name',
            'districts.name as district_name',
            'wards.name as ward_name',
        ])
            ->where('orders.code', $code)
            ->where('orders.customer_id', $customer->id)
            ->leftJoin('provinces', 'orders.province_id', '=', 'provinces.code')
            ->leftJoin('districts', 'orders.district_id', '=', 'districts.code')
            ->leftJoin('wards', 'orders.ward_id', '=', 'wards.code')
            ->with('order_products')
            ->first();

        if (!$order) {
            return redirect()->route('customer.order')
                ->with('error', 'Đơn hàng không tồn tại hoặc bạn không có quyền xem đơn hàng này.');
        }

        $system = $this->system;
        $seo = [
            'meta_title' => 'Chi tiết đơn hàng #' . $order->code . ' - ' . $customer->name,
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => route('customer.order.detail', $order->code)
        ];

        return view('frontend.auth.customer.orderDetail', compact(
            'seo',
            'system',
            'customer',
            'order',
        ));
    }

    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect()->route('home.index')->with('success', 'Bạn đã đăng xuất khỏi hệ thống.');
    }

    public function pointHistory()
    {
        $customer = Auth::guard('customer')->user();

        $histories = CustomerPointHistory::with(['order:id,code'])
            ->where('customer_id', $customer->id)
            ->orderByDesc('created_at')
            ->paginate(12);

        $typeLabels = [
            'earn' => 'Cộng điểm',
            'revertEarn' => 'Hoàn điểm đã cộng',
            'use' => 'Trừ điểm khi dùng',
            'revertUsed' => 'Hoàn điểm đã trừ',
            'revertUse' => 'Hoàn điểm đã trừ',
        ];

        $system = $this->system;
        $seo = [
            'meta_title' => 'Lịch sử giao dịch điểm - ' . $customer->name,
            'meta_keyword' => '',
            'meta_description' => '',
            'meta_image' => '',
            'canonical' => route('customer.point.history')
        ];

        return view('frontend.auth.customer.pointHistory', compact(
            'seo',
            'system',
            'customer',
            'histories',
            'typeLabels',
        ));
    }
}

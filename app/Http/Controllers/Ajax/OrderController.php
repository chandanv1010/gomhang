<?php

namespace App\Http\Controllers\Ajax;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Classes\ViettelPost;
use Illuminate\Support\Facades\Auth;

use App\Services\V1\Core\OrderService;
use App\Repositories\Core\OrderRepository;
use App\Models\Customer;
use App\Models\CustomerPointHistory;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $orderService;
    protected $orderRepository;

    public function __construct(
        OrderService $orderService,
        OrderRepository $orderRepository,
    ){
       $this->orderService = $orderService;
       $this->orderRepository = $orderRepository;
    }


    public function update(Request $request)
    {
        DB::beginTransaction();

        try {

            // 1. Update đơn hàng
            if (!$this->orderService->update($request)) {
                return response()->json([
                    'error' => 11,
                    'messages' => 'Cập nhật dữ liệu thất bại'
                ]);
            }

            // 2. Lấy lại đơn hàng
            $order = $this->orderRepository->getOrderById(
                $request->input('id'),
                ['buyers.customer_catalogues']
            );

            $customer = Customer::find($order->customer_id);

            if (!$customer) {
                throw new \Exception("Không tìm thấy khách hàng.");
            }

            $cart = $order->cart;
            $total = $cart['cartTotal'] ?? 0;

            $percent = $customer->customer_catalogues->point_percent ?? 0;
            $pointsEarned = floor($total * ($percent / 100));


            /*
            |--------------------------------------------------------------------------
            | 1. EARN POINTS – trạng thái chuyển sang PAID
            |--------------------------------------------------------------------------
            */
            if ($order->payment === 'paid' && $order->point_added == 0) {

                $customer->point += $pointsEarned;
                $customer->save();

                // Ghi số điểm thực sự cộng vào
                $order->point_value = $pointsEarned;
                $order->point_added = 1;
                $order->save();

                CustomerPointHistory::create([
                    'customer_id' => $customer->id,
                    'order_id'    => $order->id,
                    'points'      => $pointsEarned,
                    'type'        => 'earn',
                    'note'        => "Cộng điểm cho đơn hàng #{$order->code}",
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 2. USE POINTS – Trừ điểm khi đơn hàng PAID (nếu có dùng điểm)
            |--------------------------------------------------------------------------
            */
            if ($order->payment === 'paid' && $order->point_used > 0 && $order->point_used_deducted == 0) {

                $customer->point -= $order->point_used;
                if ($customer->point < 0) $customer->point = 0;
                $customer->save();

                $order->point_used_deducted = 1;
                $order->save();

                // Lịch sử trừ điểm
                CustomerPointHistory::create([
                    'customer_id' => $customer->id,
                    'order_id'    => $order->id,
                    'points'      => -$order->point_used,
                    'type'        => 'use',
                    'note'        => "Trừ điểm sử dụng cho đơn hàng #{$order->code}",
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | 3. REVERT POINTS – Hoàn điểm khi đơn rời khỏi PAID
            |--------------------------------------------------------------------------
            */
            if ($order->payment !== 'paid') {

                // Hoàn điểm earned trước đó
                if ($order->point_added == 1) {

                    $revertEarn = min($customer->point, $order->point_value);

                    $customer->point -= $revertEarn;
                    $customer->save();

                    $order->point_added = 0;
                    $order->point_value = 0;
                    $order->save();

                    CustomerPointHistory::create([
                        'customer_id' => $customer->id,
                        'order_id'    => $order->id,
                        'points'      => -$revertEarn,
                        'type'        => 'revertEarn',
                        'note'        => "Hoàn điểm cộng của đơn #{$order->code}",
                    ]);
                }
            }

            // Hoàn điểm user đã dùng
            if ($order->payment !== 'paid' && $order->point_used > 0 && $order->point_used_deducted == 1) {

                $customer->point += $order->point_used;
                $customer->save();

                $order->point_used_deducted = 0;
                $order->save();

                CustomerPointHistory::create([
                    'customer_id' => $customer->id,
                    'order_id'    => $order->id,
                    'points'      => $order->point_used,
                    'type'        => 'revertUsed',
                    'note'        => "Hoàn điểm đã dùng cho đơn #{$order->code}",
                ]);
            }

            DB::commit();

            return response()->json([
                'error' => 10,
                'messages' => 'Cập nhật đơn hàng thành công',
                'order' => $order
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'error' => 11,
                'messages' => "Lỗi xử lý: " . $e->getMessage()
            ]);
        }
    }


    public function chart(Request $request){
        $chart = $this->orderService->ajaxOrderChart($request);

        return response()->json($chart);

    }
    
   
}

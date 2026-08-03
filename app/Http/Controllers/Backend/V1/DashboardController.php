<?php

namespace App\Http\Controllers\Backend\V1;

use App\Http\Controllers\Controller;

use App\Services\V1\Core\OrderService;
use App\Repositories\Core\OrderRepository;
use App\Services\V1\Customer\CustomerService;

class DashboardController extends Controller
{


    protected $orderService;
    protected $customerService;
    protected $orderRepository;

    public function __construct(
        OrderService $orderService,
        CustomerService $customerService,
        OrderRepository $orderRepository,
    ){
        $this->orderService = $orderService;
        $this->orderRepository = $orderRepository;
        $this->customerService = $customerService;
    }

    public function index(){

        $orderStatistic = $this->orderService->statistic(); 
        $customerStatistic = $this->customerService->statistic();
        $startDate = convertDateTime( now(), 'Y-m-d 00:00:00');
        $endDate = convertDateTime( now(), 'Y-m-d 23:59:59');
        $newOrders = $this->orderRepository->newOrder($startDate, $endDate);
        $config = $this->config();
        $template = 'backend.dashboard.home.index';
        return view('backend.dashboard.layout', compact(
            'template',
            'config',
            'orderStatistic',
            'customerStatistic',
            'newOrders'
        ));
    }

    private function config(){
        return [
            'js' => [
                'backend/js/plugins/chartJs/Chart.min.js',
                'backend/library/dashboard.js',
            ]
        ];
    }

}

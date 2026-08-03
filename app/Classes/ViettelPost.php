<?php
namespace App\Classes;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Repositories\Interfaces\OrderRepositoryInterface  as OrderRepository;
use DB;

class ViettelPost{
    
    private $email;
    private $password;
    private $client;

    public function __construct(
        String $email,
        String $password,
    ){
        $this->email = $email;
        $this->password = $password;
        $this->client = new \GuzzleHttp\Client();
        
    }

    public function login(){
        $apiUrl = 'https://partner.viettelpost.vn/v2/user/Login';
        try {
            
            $response = $this->client->post($apiUrl, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'json' => [
                    'USERNAME' => $this->email,
                    'PASSWORD' => $this->password
                ]
            ]);

            $data = json_decode($response->getBody(), true);
            return $data;

        } catch (RequestException $e) {
            $this->handleErrorLog($e);
        }
    }

    public function getToken(){
        $token = session('viettelpost_token');

        if(!$token){
            $response = $this->login();
            if(isset($response['data']['token'])){
                $token = $response['data']['token'];
                session('viettelpost_token', $response['data']['token']);
            }else{
                throw new Exception("Không thể truy cập token từ viettelpost");
            }
        }

        return $token;
    }


    public function getProvinces($token = '', $provinceId = ''){
        if(!empty($token)){
            $apiUrl = 'https://partner.viettelpost.vn/v2/categories/listProvinceById?provinceId=' . $provinceId;
            try {
                $response = $this->client->get($apiUrl, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Token' => $token
                    ],
                ]);
                $data = json_decode($response->getBody(), true);
                return $data;
            } catch (\Exception $e) {
                $this->handleErrorLog($e);
            }
        }
        return null;
    }

    public function getDistricts($token , $provinceId){
        if(!empty($token)){
            $apiUrl = 'https://partner.viettelpost.vn/v2/categories/listDistrict?provinceId=' . $provinceId;
            try {
                $response = $this->client->get($apiUrl, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Token' => $token
                    ],
                ]);
                $data = json_decode($response->getBody(), true);
                return $data;
            } catch (\Exception $e) {
                $this->handleErrorLog($e);
            }
        }
    }

    public function getWards($token , $districtId){
        if(!empty($token)){
            $apiUrl = 'https://partner.viettelpost.vn/v2/categories/listWards?districtId=' . $districtId;
            try {
                $response = $this->client->get($apiUrl, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Token' => $token
                    ],
                ]);
                $data = json_decode($response->getBody(), true);
                return $data;
            } catch (\Exception $e) {
                $this->handleErrorLog($e);
            }
        }
    }

    public function shipping($buyer, $seller, $totalWeight, $totalPrice, $token){
        if(!empty($token)){
            $apiUrl = 'https://partner.viettelpost.vn/v2/order/getPriceAll';
            try {
                
                $bodyRaw = [
                    "SENDER_PROVINCE" => $seller->province_id,
                    "SENDER_DISTRICT" => $seller->district_id,
                    "RECEIVER_PROVINCE" => $buyer->province_id,
                    "RECEIVER_DISTRICT" => $buyer->province_id,
                    "PRODUCT_TYPE" => "HH",
                    "PRODUCT_WEIGHT" => $totalWeight,
                    "PRODUCT_PRICE" => $totalPrice,
                    "MONEY_COLLECTION" => $totalPrice,
                    "TYPE" => 1
                ];

                $response = $this->client->post($apiUrl, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Token' => $token
                    ],
                    'json' => $bodyRaw,
                ]);

                $data = json_decode($response->getBody(), true);

                //Ship thường
                // Trường hợp nếu muốn ship hỏa tốc thì chọn MA_DV_CHINH = VHS
                $result = array_filter($data, function($item){
                    return $item['MA_DV_CHINH'] === 'PHS';
                });
                $flatData = array_merge(...array_values($result));
                return $flatData['GIA_CUOC'];
            } catch (\Exception $e) {
                $this->handleErrorLog($e);
            }
        }
    }

    public function storeAddress($token){
        $apiUrl = 'https://partner.viettelpost.vn/v2/user/listInventory';

        
        $response = $this->client->get($apiUrl, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Token' => $token
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    public function mergeFullAddress($provinceId, $districtId, $wardId){
        // Lấy token từ session hoặc đăng nhập lại
        $token = $this->getToken();

        if (!$token) {
            throw new \Exception("Không thể lấy token");
        }

        // Lấy thông tin địa chỉ từ ViettelPost API
        $province = $this->getProvinces($token, $provinceId);
        $district = $this->getDistricts($token, $provinceId);
        $ward = $this->getWards($token, $districtId);

        // Tìm tên tỉnh
        $provinceName = $province['data'][0]['PROVINCE_NAME'] ?? '';
        // Tìm tên huyện
        $districtName = '';
        if (!empty($district['data'])) {
            foreach ($district['data'] as $d) {
                if ($d['DISTRICT_ID'] == $districtId) {
                    $districtName = $d['DISTRICT_NAME'];
                    break;
                }
            }
        }
        // Tìm tên xã
        $wardName = '';
        if (!empty($ward['data'])) {
            foreach ($ward['data'] as $w) {
                if ($w['WARDS_ID'] == $wardId) {
                    $wardName = $w['WARDS_NAME'];
                    break;
                }
            }
        }

        // Kết hợp thành địa chỉ đầy đủ
        $fullAddress = trim(sprintf('%s, %s, %s', $wardName, $districtName, $provinceName));

        return $fullAddress;
    }

    public function createOrder($token, $order, $seller, $orderRepository){
        $storeAddress = $this->storeAddress($token);
        $deliveryTime = Carbon::now()->format('d/m/Y H:i:s');

        $receiverAddress = $this->mergeFullAddress(
            $order->buyers->province_id,
            $order->buyers->district_id,
            $order->buyers->ward_id,
        );

        $orderItem = DB::table('order_product')->where('order_id', $order->id)->get();
        $listItem = [];
        if($orderItem){
            foreach($orderItem as $key => $val){
                $listItem[] = [
                    "PRODUCT_NAME" => $val->name,
                    "PRODUCT_PRICE" => $val->price,
                    "PRODUCT_WEIGHT" => 100,
                    "PRODUCT_QUANTITY" => $val->qty
                ];
            }
        }

        // dd($storeAddress);


        if(!empty($token) && $storeAddress){
            $apiUrl = 'https://partner.viettelpost.vn/v2/order/createOrder';
            try {
               
                $bodyRaw = [
                    "ORDER_NUMBER" => $order->code,
                    "GROUPADDRESS_ID" => $storeAddress['data'][0]['groupaddressId'],
                    "CUS_ID" => $storeAddress['data'][0]['cusId'],
                    "DELIVERY_DATE" => $deliveryTime,
                    "SENDER_FULLNAME" => $seller->name,
                    "SENDER_ADDRESS" => $storeAddress['data'][0]['address'],
                    "SENDER_PHONE" => $storeAddress['data'][0]['phone'],
                    "SENDER_EMAIL" => $seller->email,
                    "SENDER_WARD" => $seller->ward_id,
                    "SENDER_DISTRICT" => $seller->district_id,
                    "SENDER_PROVINCE" => $seller->province_id,
                    "SENDER_LATITUDE" => 0,
                    "SENDER_LONGITUDE" => 0,
                    "RECEIVER_FULLNAME" => $order->buyers->name,
                    "RECEIVER_ADDRESS" => $receiverAddress,
                    "RECEIVER_PHONE" => $order->buyers->phone,
                    "RECEIVER_EMAIL" => $order->buyers->email,
                    "RECEIVER_WARD" => $order->buyers->ward_id,
                    "RECEIVER_DISTRICT" => $order->buyers->district_id,
                    "RECEIVER_PROVINCE" => $order->buyers->province_id,
                    "RECEIVER_LATITUDE" => 0,
                    "RECEIVER_LONGITUDE" => 0,
                    "PRODUCT_NAME" => "Đơn hàng mã " . $order->code,
                    "PRODUCT_DESCRIPTION" => $order->description,
                    "PRODUCT_QUANTITY" => $order['cart']['cartTotalItems'],
                    "PRODUCT_PRICE" => $order['cart']['cartTotal'],
                    "PRODUCT_WEIGHT" => 100, // default 100g --> chỗ này của API có vẻ là tổng khối lượng
                    "PRODUCT_LENGTH" => 38,
                    "PRODUCT_WIDTH" => 24,
                    "PRODUCT_HEIGHT" => 25,
                    "PRODUCT_TYPE" => "HH",
                    "ORDER_PAYMENT" => 3, // Thu tiền hàng 
                    "ORDER_SERVICE" => "VCBA",
                    "ORDER_SERVICE_ADD" => "",
                    "ORDER_VOUCHER" => "",
                    "ORDER_NOTE" => "cho xem hàng, không cho thử",
                    "MONEY_COLLECTION" => $order['cart']['cartTotal'], // Tiền thu hộ 
                    "MONEY_TOTALFEE" => 0,
                    "MONEY_FEECOD" => 0,
                    "MONEY_FEEVAS" => 0,
                    "MONEY_FEEINSURRANCE" => 0,
                    "MONEY_FEE" => 0,
                    "MONEY_FEEOTHER" => 0,
                    "MONEY_TOTALVAT" => 0,
                    "MONEY_TOTAL" => 0,
                    "LIST_ITEM" => $listItem
                ];


                $response = $this->client->post($apiUrl, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Token' => $token
                    ],
                    'json' => $bodyRaw,
                ]);

                $data = json_decode($response->getBody(), true);

                return $data;
              
            } catch (\Exception $e) {
                $this->handleErrorLog($e);
            }
        }
    }

    private function handleErrorLog($e){

        echo $e->getMessage();die();
       
        throw new \Exception('Cannot connect to ViettelPost API');
    }

    
	
}

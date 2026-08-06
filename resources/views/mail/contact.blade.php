<!DOCTYPE html>
<html>
    <head>
        <style>
            .cart-success{
                padding: 30px 10px;
            }
            @media (min-width: 1220px){
                .cart-success{
                    width:800px;
                    margin:0 auto;
                }
            }
            .cart-success .cart-heading{
                text-align: center;
                margin-bottom:30px;
            }
            .cart-success .cart-heading > span{
                text-transform: uppercase;
                font-weight: 700;
            }
            .discover-text > *{
                display: inline-block;
                padding:10px 25px;
                background: #d61c00;
                border-radius: 16px;
                cursor:pointer;
                color:#fff;
            }
            .discover-text{
                text-align: center;
            }
            .checkout-box{
                border:1px solid #000;
                padding:15px 20px;
                border-radius: 16px;
            }
            .cart-success .panel-body{
                margin-bottom:40px;
            }
            .checkout-box-head{
                margin-bottom:30px;
            }

            .checkout-box-head .order-title{
                border:1px solid #000;
                border-radius: 16px;
                padding:10px 15px;
                font-weight: 700;
                font-size:16px;
            }
            .checkout-box-head .order-date{
                display: flex;
                align-items: center;
                font-size:16px;
                font-weight: bold;
                text-align: center;
            }
            .cart-success .table{
                width:100%;
                border-spacing: 0;
                background: #d9d9d9;
                border-collapse: inherit;
            }
            .cart-success .table thead>tr th{
                color:#fff;
                background: #d61c00;
                font-weight: 500;
                font-size:14px;
                vertical-align: middle;
                border-bottom: 2px solid #dee2e6;
                text-align: center;
                border:none;
                padding:12px 15px;
            }
            .cart-success tbody tr td{
                padding:12px 15px;
                vertical-align: middle;
                font-size: 14px;
                color:#000;
                border-bottom:1px solid #ccc;
            }
            .cart-success tfoot{
                background: #fff;
            }
            .cart-success tfoot tr td{
                padding:8px;
            }

            .cart-success .table td:last-child{
                text-align: right;
            }
            .cart-success .table tbody tr:nth-child(2n) td {
                background-color: #d9d9d9;
            }
            .total_payment{
                font-weight: bold;
                font-size:24px;
            }
            .panel-foot .checkout-box div{
                margin-bottom:15px;
                font-weight: 500;
            }
            .uk-text-left{
                text-align: left;
            }
            .uk-text-right{
                text-align: right;
            }
            .uk-text-center{
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="cart-success">
            <div class="panel-foot">
                <h2 class="cart-heading"><span>Thông tin liên hệ</span></h2>
                <div class="checkout-box">
                    <div>Họ tên : {{ $data['name'] }}<span></span></div>
                    <div>Địa chỉ: {{ $data['address'] }}<span></span></div>
                    <div>Số điện thoại: {{ $data['phone'] }}<span></span></div>
                    @if(isset($data['product_id']))
                    <div>Sản phẩm : {{ $data['product_name'] ?? null }}</div>
                    <div>Loại : {{ $data['type'] ? 'Đặt hàng' : 'Tư vấn sản phẩm' }}<span></span></div>
                    <div>Showroom gần nhất : {{ $data['post_id'] ?? null }}</div>
                    @else
                    <div>Bài : {{ $data['product_name'] ?? null }}</div>
                    @endif
                   
                </div>
            </div>
        </div>
    </body>
</html>
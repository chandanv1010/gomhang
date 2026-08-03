@extends('frontend.homepage.layout')
@section('content')

    {{-- Breadcrumb Hero --}}
    <div class="cat-hero-section" style="background-image: url('/vendor/frontend/img/project/breadcrumb.png');">
        <div class="cat-hero-overlay"></div>
        <div class="cat-hero-shapes">
            <div class="shape shape-left"></div>
            <div class="shape shape-right"></div>
        </div>
        <div class="uk-container uk-container-center cat-hero-container">
            <h1 class="cat-hero-title">Hệ Thống Phân Phối</h1>
            <ul class="uk-list uk-clearfix uk-flex uk-flex-middle uk-flex-center cat-hero-breadcrumbs">
                <li><a href="/">Trang chủ</a></li>
                <li class="separator">&raquo;</li>
                <li><a href="#" onclick="return false;">Hệ thống phân phối</a></li>
            </ul>
        </div>
    </div>

    <section class="distribution-page-wrapper">
        <div class="uk-container uk-container-center">
            
            {{-- Filter Area --}}
            <div class="distribution-filter-box">
                <form action="{{ route('frontend.distribution.index') }}" method="GET" id="distributor-filter-form" class="uk-form">
                    <div class="uk-grid uk-grid-medium" data-uk-grid-margin>
                        <div class="uk-width-large-1-3 uk-width-medium-1-2 uk-width-1-1">
                            <div class="filter-field">
                                <label class="field-label">Chọn khu vực (Miền)</label>
                                <select name="province_id" id="region-select" class="uk-width-1-1 filter-select">
                                    <option value="0">Tất cả các miền</option>
                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}" {{ $selectedRegion == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3 uk-width-medium-1-2 uk-width-1-1">
                            <div class="filter-field">
                                <label class="field-label">Chọn Tỉnh / Thành phố</label>
                                <select name="district_id" id="city-select" class="uk-width-1-1 filter-select">
                                    <option value="0">Chọn Tỉnh / Thành phố</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ $selectedCity == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="uk-width-large-1-3 uk-width-medium-1-1 uk-width-1-1 uk-flex uk-flex-bottom">
                            <button type="submit" class="btn-filter-submit uk-width-1-1">
                                <i class="fa fa-filter"></i> Lọc Kết Quả
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Main directory layout --}}
            <div class="uk-grid uk-grid-medium distribution-grid" data-uk-grid-margin>
                
                {{-- Left list --}}
                <div class="uk-width-large-4-10 uk-width-medium-1-1 uk-width-1-1 list-col">
                    <div class="distributors-list">
                        @if(count($distributors) > 0)
                            @foreach($distributors as $index => $distributor)
                                <div class="distributor-card {{ $index == 0 ? 'active' : '' }}" 
                                     data-map="{{ base64_encode($distributor->map) }}" 
                                     data-id="{{ $distributor->id }}">
                                    @if($distributor->image)
                                        <div class="distributor-img-box">
                                            <img src="{{ $distributor->image }}" alt="{{ $distributor->name }}">
                                        </div>
                                    @endif
                                    <div class="distributor-info">
                                        <h3 class="distributor-name">{{ $distributor->name }}</h3>
                                        <div class="info-item">
                                            <span class="icon"><i class="fa fa-map-marker"></i></span>
                                            <span class="text">{{ $distributor->address }}</span>
                                        </div>
                                        <div class="info-item">
                                            <span class="icon"><i class="fa fa-phone"></i></span>
                                            <span class="text"><a href="tel:{{ $distributor->phone }}">{{ $distributor->phone }}</a></span>
                                        </div>
                                        @if($distributor->email)
                                            <div class="info-item">
                                                <span class="icon"><i class="fa fa-envelope"></i></span>
                                                <span class="text"><a href="mailto:{{ $distributor->email }}">{{ $distributor->email }}</a></span>
                                            </div>
                                        @endif
                                        <div class="card-footer">
                                            <button class="btn-view-map"><i class="fa fa-map"></i> Xem bản đồ</button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="no-distributor">
                                <i class="fa fa-info-circle"></i>
                                <p>Không có nhà phân phối nào phù hợp với bộ lọc tìm kiếm.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right map --}}
                <div class="uk-width-large-6-10 uk-width-medium-1-1 uk-width-1-1 map-col">
                    <div class="map-sticky-container" id="map-sticky">
                        <div class="map-iframe-wrapper" id="active-map-box">
                            {{-- Placeholder or initial map --}}
                            @if(count($distributors) > 0)
                                {!! $distributors[0]->map !!}
                            @else
                                <div class="map-placeholder">
                                    <i class="fa fa-map-o"></i>
                                    <p>Vui lòng chọn nhà phân phối để xem vị trí bản đồ.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <style>
        .distribution-page-wrapper {
            padding: 60px 0 80px;
            background: #f8f9fa;
        }

        /* Filter panel */
        .distribution-filter-box {
            background: #fff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 40px;
            border-left: 5px solid #1e4794;
        }

        .filter-field {
            margin-bottom: 10px;
        }

        .field-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .filter-select {
            height: 45px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            padding: 0 15px;
            font-size: 14px;
            color: #495057;
            background-color: #f8f9fa;
            outline: none;
            transition: all 0.3s ease;
            font-family: var(--second-font), sans-serif !important;
        }

        .filter-select:focus {
            border-color: #1e4794;
            background-color: #fff;
        }

        .btn-filter-submit {
            height: 45px;
            background: linear-gradient(135deg, #f27a24, #e66b15);
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: var(--second-font), sans-serif !important;
        }

        .btn-filter-submit:hover {
            background: linear-gradient(135deg, #1e4794, #2557b0);
            box-shadow: 0 5px 15px rgba(30,71,148,0.25);
        }

        /* Directory layout */
        .distribution-grid {
            position: relative;
        }

        .list-col {
            height: 650px;
            overflow-y: auto;
            padding-right: 10px;
        }

        /* Custom scrollbar for list col */
        .list-col::-webkit-scrollbar {
            width: 6px;
        }
        .list-col::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .list-col::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }
        .list-col::-webkit-scrollbar-thumb:hover {
            background: #999;
        }

        .distributors-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .distributor-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.03);
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            gap: 15px;
        }

        .distributor-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            border-color: rgba(30,71,148,0.3);
        }

        .distributor-card.active {
            border-color: #1e4794;
            background: #f8fbff;
            box-shadow: 0 5px 18px rgba(30,71,148,0.08);
        }

        .distributor-img-box {
            width: 80px;
            height: 80px;
            flex-shrink: 0;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #eee;
        }

        .distributor-img-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .distributor-info {
            flex-grow: 1;
        }

        .distributor-name {
            font-size: 16px;
            font-weight: 700;
            color: #1e4794;
            margin-top: 0;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .distributor-card.active .distributor-name {
            color: #f27a24;
        }

        .info-item {
            display: flex;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 13.5px;
            color: #555;
            line-height: 1.4;
        }

        .info-item .icon {
            color: #f27a24;
            flex-shrink: 0;
            width: 15px;
            text-align: center;
        }

        .info-item .text a {
            color: #555;
            text-decoration: none;
        }
        .info-item .text a:hover {
            color: #1e4794;
        }

        .card-footer {
            margin-top: 15px;
        }

        .btn-view-map {
            background: #f1f3f5;
            border: none;
            color: #1e4794;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: var(--second-font), sans-serif !important;
        }

        .distributor-card.active .btn-view-map,
        .distributor-card:hover .btn-view-map {
            background: #1e4794;
            color: #fff;
        }

        /* Map sticky container */
        .map-col {
            position: relative;
        }

        .map-sticky-container {
            position: sticky;
            top: 20px;
            background: #fff;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.06);
            border: 1px solid #eef0f3;
            height: 650px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        .map-iframe-wrapper {
            border-radius: 8px;
            overflow: hidden;
            flex-grow: 1;
            background: #eaeaea;
            display: flex;
            flex-direction: column;
        }

        .map-iframe-wrapper iframe {
            width: 100% !important;
            height: 100% !important;
            border: 0;
        }

        .map-placeholder, .no-distributor {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #888;
            padding: 40px 20px;
            text-align: center;
        }

        .map-placeholder i, .no-distributor i {
            font-size: 48px;
            color: #ccc;
            margin-bottom: 15px;
        }

        @media (max-width: 959px) {
            .list-col {
                height: auto;
                max-height: 500px;
                margin-bottom: 30px;
            }
            .map-sticky-container {
                position: static;
                height: auto;
            }
            .map-iframe-wrapper {
                height: 400px;
            }
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Interactive map loading on click
            $('.distributor-card').on('click', function() {
                var $this = $(this);
                
                // Active status swap
                $('.distributor-card').removeClass('active');
                $this.addClass('active');
                
                // Get encoded map iframe
                var encodedMap = $this.data('map');
                if (encodedMap) {
                    var decodedMap = atob(encodedMap);
                    
                    // Fade effect & insert map
                    $('#active-map-box').fadeOut(150, function() {
                        $(this).html(decodedMap).fadeIn(200);
                    });
                }
            });

            // Dynamic location dropdown filters
            var regionSelect = document.getElementById('region-select');
            var citySelect = document.getElementById('city-select');

            if (regionSelect && citySelect) {
                regionSelect.addEventListener('change', function() {
                    var parentId = this.value;
                    citySelect.innerHTML = '<option value="0">Đang tải...</option>';

                    if (parentId > 0) {
                        fetch('{{ route("frontend.ajax.distribution.getProvinces") }}?parent_id=' + parentId)
                            .then(response => response.json())
                            .then(data => {
                                var html = '<option value="0">Chọn Tỉnh / Thành phố</option>';
                                data.forEach(function(item) {
                                    html += '<option value="' + item.id + '">' + item.name + '</option>';
                                });
                                citySelect.innerHTML = html;
                            })
                            .catch(error => {
                                console.error('Lỗi khi tải khu vực:', error);
                                citySelect.innerHTML = '<option value="0">Lỗi tải dữ liệu</option>';
                            });
                    } else {
                        citySelect.innerHTML = '<option value="0">Chọn Tỉnh / Thành phố</option>';
                    }
                });
            }
        });
    </script>

@endsection

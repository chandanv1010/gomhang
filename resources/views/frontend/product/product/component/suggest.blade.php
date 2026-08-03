<div id="suggest" class="uk-modal suggest-aj" aria-hidden="true">
    <div class="uk-modal-dialog">
        <div class="panel-suggest">
            <div class="uk-grid uk-grid-medium">
                <div class="uk-width-medium-1-3">
                    <div class="info-product">
                        <h3 class="heading-2">
                            <span>{{ $payload->name }}</span>
                            <div class="border-remark"></div>
                        </h3>
                        <a href="" class="image img-cover">
                            <img src="{{ $payload->image }}" alt="">
                        </a>
                        <div class="product-price">
                            {!! $price['html'] ?? null !!} 
                        </div>
                        {{-- <div class="description">
                            {!! $payload->description !!}
                        </div> --}}
                    </div>
                </div>
                <div class="uk-width-medium-2-3">
                    <form action="" class="info-form">
                        <div class="panel-head">
                            <h2 class="heading-1">
                                Nhập thông tin của bạn
                                <div class="border-remark"></div>
                            </h2>
                            <p>(Để chúng tôi phục vụ bạn chu đáo hơn )</p>
                        </div>
                        <div class="panel-body">
                            <div class="up mb20">
                                <div class="fr">
                                    <input type="text" name="name" placeholder="Tên quý khách" class="mr10">
                                    <span class="error-message name-error"></span>
                                </div>
                                <div class="flex items-center gap-2 border px-3 bg-light w-fit">
                                    <div class="flex items-center gap-2 uk-flex uk-flex-middle">
                                        <input id="male" type="radio" value="male" checked="" name="gender" class="" >
                                        <label for="male">Anh</label>
                                        <input id="female" type="radio" value="female" checked="" name="gender" class="" >
                                        <label for="female">Chị</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row mb20">
                                <input placeholder="Số điện thoại" name="phone" value="" class="border h-10 text-sm p-2.5 w-full" on:input="q-D8CXyDHq.js#s_WQShqIriXzI[0 1 2 3]" on:change="q-D8CXyDHq.js#s_vNtVq2dMPhY[0 1 2]" on:blur="q-D8CXyDHq.js#s_fzfym1ErEFI[0 1 2]" q:id="8m">
                                <span class="error-message phone-error"></span>
                            </div>
                            <div class="form-row mb20">
                                <input placeholder="Địa chỉ" name="address" value="" class="border h-10 text-sm p-2.5 w-full" on:input="q-D8CXyDHq.js#s_WQShqIriXzI[0 1 2 3]" on:change="q-D8CXyDHq.js#s_vNtVq2dMPhY[0 1 2]" on:blur="q-D8CXyDHq.js#s_fzfym1ErEFI[0 1 2]" q:id="8q">
                            </div>
                            @if(isset($widgets['showroom-system']))
                                @foreach ($widgets['showroom-system']->object as $item)
                                    <div class="showroom-sys mb20">
                                        <p>Chọn showroom gần bạn nhất:</p>
                                        @foreach ($item->posts  as $key => $post)
                                            <div class="sys-item">
                                                <input id="{{ $post->id }}" type="radio" value="{{ $post->id }}" checked="" name="post_id" class="">
                                                <label for="{{ $post->id }}">{{ $post->languages->first()->pivot->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            @endif
                            <input type="hidden" name="{{ (isset($field)) ? $field : 'product_id' }}" value="{{ $payload->id }}">
                            <button type="submit" class="advise">Gửi tin tư vấn</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
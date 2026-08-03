
<!-- This is the modal -->
<div id="my-qr" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        <div class="modal-content">
            {!! $product->qrcode !!}
        </div>
    </div>
</div>

<!-- This is the modal -->
<div id="my-desc" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        <div class="modal-content">
            <h2 class="heading-2"><span>Mô tả sản phẩm</span></h2>
            <hr>
            <div class="dialog-description">
                {!! $product->description !!}
            </div>
        </div>
    </div>
</div>

<!-- This is the modal -->
<div id="my-tech" class="uk-modal">
    <div class="uk-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        <div class="modal-content">
            Đang update....
        </div>
    </div>
</div>

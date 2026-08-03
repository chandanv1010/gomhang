<div class="hd-menu-search top-search bg-theme" style="display: none;">
    <div class="uk-container uk-container-center">
        <form action="tim-kiem" method="get" class="uk-form form">
            <div class="uk-flex uk-flex-middle">
                <button type="submit" name="" value="" class="btn-submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                <div class="form-row">
                    <input type="text" name="keyword" class="uk-width-1-1 input-text" placeholder="Type & hit enter..." />
                </div>
                <a class="btn-close"><i class="fa fa-times" aria-hidden="true"></i></a>
            </div>
        </form>
    </div>
</div>

<style>
	.top-search {
	    padding: 10px 0;
	    display: none;
	}
	.bg-theme {
	    background-color: #971b1e;
	}
	.top-search .form .form-row {
	    padding: 0 10px;
	    width: 100%;
	}
	.top-search .form .input-text {
	    padding: 0 10px;
	    background: transparent;
	    border: 0;
	    outline: 0;
	    color: #fff;
	}
	.top-search .btn-submit {
	    background: transparent;
	    border: 0;
	    outline: 0;
	    color: #fff;
	    font-size: 16px;
	}
	.top-search .btn-close {
	    color: #fff;
	    font-size: 18px;
	}

/*============================== .hd-menu-search ==============================*/
	.hd-menu-search{
		position: relative;
		text-align: center;
	}

	.hd-menu-search .icon {
	    color: #232323;
	    font-size: 16px;
	}

	.dropdown-search .form .input-text {
		display: block;
		position: relative;
		padding: 0 25px 0 10px;
		height: 32px;
		width: 275px;
		background: #fff;
		border: none;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
		-ms-border-radius: 5px;
		-o-border-radius: 5px;
		border-radius: 5px;
		color: #333;
		font-size: 13px;
		line-height: 32px;
		-webkit-transition: all .35s ease;
		-moz-transition: all .35s ease;
		-o-transition: all .35s ease;
		transition: all .35s ease;
	}
	.dropdown-search .form .btn-submit {
	    display: block;
	    position: absolute;
	    left: initial;
	    right: 10px;
	    top: 50%;
	    transform: translate(0, -50%);
	    border: none;
	    cursor: pointer;
	    outline: none;
	    background: #fff;
	    font-size: 16px;
	}
	.dropdown-search .form .input-text::-webkit-input-placeholder {
	    color: #868686;
	    font-size: 12px;
	}
	.dropdown-search .form .input-text::-moz-placeholder {
	    color: #868686;
	    font-size: 12px;
	}
	.dropdown-search .form .input-text:-ms-input-placeholder {
	    color: #868686;
	    font-size: 12px;
	}
	.dropdown-search .form .input-text:-moz-placeholder {
	    color: #868686;
	    font-size: 12px;
	}

	.dropdown-search{
		opacity: 0;
		visibility: hidden;
		position: absolute;
		top: calc(100% + 3px);
		right: 0;
		/*transform: translate(-81% , 0);*/
		/*border-top: 3px solid #f96840;*/
	    /*background: linear-gradient(to bottom, #0a5193, #1163a9 50%, #0a5193 50%, #0e61a7);*/
		padding: 6px;
		/*-webkit-box-shadow: 0 2px 3px rgba(0,0,0,0.4);
		-moz-box-shadow: 0 2px 3px rgba(0,0,0,0.4);
		-ms-box-shadow: 0 2px 3px rgba(0,0,0,0.4);
		-o-box-shadow: 0 2px 3px rgba(0,0,0,0.4);
		box-shadow: 0 2px 3px rgba(0,0,0,0.4);*/
		transition: all 0.5s ease-in-out;
	}

	.dropdown-search.active {
	    /* transform: translate(-81% , 0); */
	    opacity: 1;
	    visibility: visible;
	    z-index: 99;
	}


	.opacity-extra-medium {
	    position: absolute;
	    height: 100%;
	    width: 100%;
	    opacity: 0.5;
	    top: 0;
	    left: 0;
	    z-index: 1;
	}
	.top-search .form .input-text::-webkit-input-placeholder {
	    color: #fff;
	    font-size: 13px;
	}
	.top-search .form .input-text::-moz-placeholder {
	    color: #fff;
	    font-size: 13px;
	}
	.top-search .form .input-text:-ms-input-placeholder {
	    color: #fff;
	    font-size: 13px;
	}
	.top-search .form .input-text:-moz-placeholder {
	    color: #fff;
	    font-size: 13px;
	}

</style>

<script>
	var actual_link = $(location).attr('pathname');
	actual_link = actual_link.substr(1);


	$(document).on('click', '.open-search', function(event) {
	    event.preventDefault();
	    let _this = $(this);
	  	_this.toggleClass('active');
    	// _this.siblings('.dropdown-search').toggleClass('active');
    	$('.top-search').toggleClass('active');
    	$('.top-search').slideToggle();

	    return false;
	});

	$(document).on('click', '.btn-close', function(event) {
	    $(this).closest('header').find('.open-search').trigger('click');
	});

	 /*body click*/
	$('body').click(function(e) {
	  	var target = $(e.target);
	  	if((!target.is('.hd-menu-search') && $('.hd-menu-search').has(e.target).length === 0))
	  	{
		   	if ( $('.open-search').hasClass('active')){
			    $('.open-search').removeClass('active');
				e.preventDefault();
		  	}
			if ( $('.top-search').hasClass('active')){
			    $('.top-search').removeClass('active');
			    $('.top-search').slideUp();
				e.preventDefault();
		  	}
	  	}

	});

	// $('.main-menu>li').each(function(){
	// 	let _this = $(this);

	// 	let url = _this.attr('data-url');
	// 	if(actual_link == url){
	// 		_this.addClass('active');
	// 	}
	// });
</script>

(function($){
var HT = {};

// Dia gioi sau sap nhap (01/07/2025): Tinh/Thanh pho -> Phuong/Xa (khong con cap huyen).
HT.getLocation = () => {
    $(document).on('change', '.location', function(){
        let _this = $(this)
        let option = {
            'data' : {
                'location_id' : _this.val(),
            },
            'target' : 'wards'
        }

        HT.sendDataTogetLocation(option)
    })
}

HT.sendDataTogetLocation = (option) => {
    $.ajax({
        url: 'ajax/location/getLocation', 
        type: 'GET', 
        data: option,
        dataType: 'json', 
        success: function(res) {

            $('.wards').html(res.html)

            if(typeof ward_id !== 'undefined' && ward_id != ''){
                $('.wards').val(ward_id).trigger('change')
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Lỗi: ' + textStatus + ' ' + errorThrown);
        }
    });
}

HT.loadCity = () => {
    if(typeof province_id !== 'undefined' && province_id != ''){
        $(".province").val(province_id).trigger('change');
    }
}


// jQuery 3 khong con su kien 'ready' qua .on() - handler cu khong bao gio chay.
$(function(){
    HT.getLocation();
    HT.loadCity();
})
})(jQuery);

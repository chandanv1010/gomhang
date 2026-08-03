var HT = {}; 
var _token = $('meta[name="csrf-token"]').attr('content');

HT.switchery = () => {
    $('.js-switch').each(function(){
        // let _this = $(this)
        var switchery = new Switchery(this, { color: '#1AB394', size: 'small'});
    })
}


HT.select2 = () => {
    if($('.setupSelect2').length){
        $('.setupSelect2').select2();
    }
    
}

HT.sortui = () => {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
}

HT.changeStatus = () => {
    $(document).on('change', '.status', function(e){

        let _this = $(this)
        let option = {
            'value' : _this.val(),
            'modelId' : _this.attr('data-modelId'),
            'model' : _this.attr('data-model'),
            'field' : _this.attr('data-field'),
            'namespace': _this.attr('data-namespace'),
            '_token' : _token
        }

        console.log(option);
        

        $.ajax({
            url: 'ajax/dashboard/changeStatus', 
            type: 'POST', 
            data: option,
            dataType: 'json', 
            success: function(res) {
                let inputValue = ((option.value == 1)?2:1)
                if(res.flag == true){
                    _this.val(inputValue)
                }
                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                
                console.log('Lỗi: ' + textStatus + ' ' + errorThrown);
            }
        });

        e.preventDefault()
    })
}

HT.changeStatusAll = () => {
    if($('.changeStatusAll').length){
        $(document).on('click', '.changeStatusAll', function(e){
            let _this = $(this)
            let id = []
            $('.checkBoxItem').each(function(){
                let checkBox = $(this)
                if(checkBox.prop('checked')){
                    id.push(checkBox.val())
                }
            })

            let option = {
                'value' : _this.attr('data-value'),
                'model' : _this.attr('data-model'),
                'field' : _this.attr('data-field'),
                'id'    : id,
                '_token' : _token
            }

            $.ajax({
                url: 'ajax/dashboard/changeStatusAll', 
                type: 'POST', 
                data: option,
                dataType: 'json', 
                success: function(res) {
                    if(res.flag == true){
                        let cssActive1 = 'background-color: rgb(26, 179, 148); border-color: rgb(26, 179, 148); box-shadow: rgb(26, 179, 148) 0px 0px 0px 16px inset; transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s, background-color 1.2s ease 0s;';
                        let cssActive2 = 'left: 13px; background-color: rgb(255, 255, 255); transition: background-color 0.4s ease 0s, left 0.2s ease 0s;';
                        let cssUnActive = 'background-color: rgb(255, 255, 255); border-color: rgb(223, 223, 223); box-shadow: rgb(223, 223, 223) 0px 0px 0px 0px inset; transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s;'
                        let cssUnActive2 = 'left: 0px; transition: background-color 0.4s ease 0s, left 0.2s ease 0s;'

                        for(let i = 0; i < id.length; i++){
                            if(option.value == 2){
                                $('.js-switch-'+id[i]).find('span.switchery').attr('style', cssActive1).find('small').attr('style', cssActive2)
                            }else if(option.value == 1){
                                $('.js-switch-'+id[i]).find('span.switchery').attr('style', cssUnActive).find('small').attr('style', cssUnActive2)
                            }
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    
                    console.log('Lỗi: ' + textStatus + ' ' + errorThrown);
                }
            });

            e.preventDefault()
        })
    }
}

HT.checkAll = () => {
    if($('#checkAll').length){
        $(document).on('click', '#checkAll', function(){
            let isChecked = $(this).prop('checked')
            $('.checkBoxItem').prop('checked', isChecked);
            $('.checkBoxItem').each(function(){
                let _this = $(this)
                HT.changeBackground(_this)
            })
        })
    }
}

HT.checkBoxItem = () => {
    if($('.checkBoxItem').length){
        $(document).on('click', '.checkBoxItem', function(){
            let _this = $(this)
            HT.changeBackground(_this)
            HT.allChecked()
        })
    }
}

HT.changeBackground = (object) => {
    let isChecked = object.prop('checked')
    if(isChecked){
        object.closest('tr').addClass('active-bg')
    }else{
        object.closest('tr').removeClass('active-bg')
    }
}

HT.allChecked = () => {
    let allChecked = $('.checkBoxItem:checked').length === $('.checkBoxItem').length;
    $('#checkAll').prop('checked', allChecked);
}

HT.int = () => {
    $(document).on('change keyup blur', '.int', function(){
        let _this = $(this)
        let value = _this.val()
        if(value === ''){
            $(this).val('0')
        }
        value = value.replace(/\./gi, "")
        _this.val(HT.addCommas(value))
        if(isNaN(value)){
            _this.val('0')
        }
    })

    $(document).on('keydown', '.int', function(e){
        let _this = $(this)
        let data = _this.val()
        if(data == 0){
            let unicode = e.keyCode || e.which;
            if(unicode != 190){
                _this.val('')
            }
        }
    })
}



HT.addCommas = (nStr) => { 
    nStr = String(nStr);
    nStr = nStr.replace(/\./gi, "");
    let str ='';
    for (let i = nStr.length; i > 0; i -= 3){
        let a = ( (i-3) < 0 ) ? 0 : (i-3);
        str= nStr.slice(a,i) + '.' + str;
    }
    str= str.slice(0,str.length-1);
    return str;
}

HT.setupDatepicker = () => {
    if($('.datepicker').length){
        $('.datepicker').datetimepicker({
            timepicker:true,
            format:'d/m/Y H:i',
            minDate:new Date(),
        });
    }
    
}


HT.setupDateRangePicker = () => {
    if($('.rangepicker').length > 0){
        $('.rangepicker').daterangepicker({
            timePicker: true,
            locale: {
                format: 'dd-mm-yy'
            }
        })
    }
}


HT.approve = () => {
    $(document).on('change', '#status-review', function(e){
        e.preventDefault()
        let _this = $(this)
        let id = _this.data('id')
        let status = _this.val()
        $.ajax({
            url: 'ajax/review/changeStatus', 
            type: 'POST', 
            data: {
                '_token' : _token,
                'id' : id,
                'status' : status
            },
            dataType: 'json', 
            success: function(res) {
                if(res){
                    toastr.success('Cập nhật trạng thái thành công !')
                    _this.closest('tr').find('#review-link').removeClass()
                }
            },
        })
    })
}

HT.changeOrder = () => {
    $(document).on('change','.sort-order', function(){
        let _this = $(this)
        let option = {
            id : _this.data('id'),
            model : _this.data('model'),
            order : _this.val(),
        }
        $.ajax({
            url: 'ajax/product/updateOrder', 
            type: 'GET', 
            data: option,
            dataType: 'json', 
            success: function(res) {
                if(res.code === 10){
                    toastr.success('Cập nhật thứ tự thành công', 'Thông báo từ hệ thống!')
                }
            },
            beforeSend: function() {
                
            },
        });
    })
}

HT.exportExcel = () => {
    $(document).on('click', '#confirmExport', function(e){
        e.preventDefault()
        let _this = $(this)
        const model = _this.data('model')
        let option = {
            model:model
        }
        HT.setupDataForExport(option)
    })
}

HT.setupDataForExport = (option) => {
    const loadingOverlay = $('<div class="loading-overlay">Đang tải file...</div>');
    $('body').append(loadingOverlay);
    const startDate = $('input[name="startDate"]').val();
    const endDate = $('input[name="endDate"]').val();

    $.ajax({
        url: 'ajax/excel/export', 
        type: 'POST', 
        data: {
            ...option,
            startDate : startDate,
            endDate : endDate,
            _token: $('meta[name="csrf-token"]').attr('content') // Thêm CSRF token
        },
        dataType: 'json', 
        success: function(res) {
            if (res.status === 'success') {
                const link = document.createElement('a');
                link.href = res.file_url;
                link.download = res.filename; 
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                console.error('Error:', res.message);
            }

            loadingOverlay.remove();
            window.location.reload()
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error('AJAX Error:', textStatus, errorThrown);
            loadingOverlay.remove();
        }
    });
}

HT.sort = () => {
    if($('.change-order').length > 0){
        $(document).on('change', '.change-order', function(){
            let _this = $(this)
            let data = _this.data()
             $.ajax({
                url: 'ajax/sort', 
                type: 'POST', 
                data: {
                    ...data,
                    value: _this.val(),
                    _token: $('meta[name="csrf-token"]').attr('content') // Thêm CSRF token
                },
                dataType: 'json', 
                success: function(res) {
                    
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('AJAX Error:', textStatus, errorThrown);
                }
            });
            
        })
    }
}

HT.changeFieldStatus = () => {
    $(document).on('change', '.change-status', function(e){
        const _this = $(this)
        const data = _this.data();

        $.ajax({
            url: 'ajax/changeStatusField', 
            type: 'POST', 
            data: {
                ...data,
                value: _this.val(),
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json', 
            success: function(res) {
                
                let inputValue = ((_this.val() == 1)?2:1)
                _this.val(inputValue)
                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                
                console.log('Lỗi: ' + textStatus + ' ' + errorThrown);
            }
        });

        e.preventDefault()
    })
}

 HT.addProgram = () => {
    $(document).on('click', '.add-policy', function(){

        let chapterIndex = $('.policy-wrapper').length

        let textareaId = `ckPolicy_${chapterIndex}`; 

        let html = `<div class="ibox mt20 policy-wrapper" data-chapter-index=${chapterIndex}>
            <div class="ibox-title">
                <div class="uk-flex uk-flex-middle uk-flex-space-between mb15">
                    <input type="text" name="scholar_policy[${chapterIndex}][title]" class="form-control" placeholder="Nhập vào tên chính sách" value="" style="width:75%;">
                    <div class="chapter-action">
                        <button type="button" class="remove-policy-item">Xóa chính sách</button>
                    </div>
                </div>
                <div class="form-row">
                    <label for="" class="control-label text-left">Nội dung</label>
                    <textarea name="scholar_policy[${chapterIndex}][description]" class="ck-editor" id="${textareaId}" placeholder="Nhập mô tả chính sách" style="width:100%; margin-top:10px;"></textarea>
                </div>
            </div>
        </div>`
        $('.program-content').append(html)
        CKEDITOR.replace(textareaId);
    })
}

HT.removeProgram = () => {
    $(document).on('click', '.remove-policy-item', function(){
        let _this = $(this)
        _this.parents('.policy-wrapper').remove()
    })
}

 HT.addQuestion= () => {
    $(document).on('click', '.add-question', function(){

        let questionIndex = $('.question-wrapper').length

        let textareaId = `ckQuestion_${questionIndex}`; 

        let html = `<div class="ibox mt20 question-wrapper" data-question-index=${questionIndex}>
            <div class="ibox-title">
                <div class="uk-flex uk-flex-middle uk-flex-space-between mb15">
                    <input type="text" name="question_answer[${questionIndex}][question]" class="form-control" placeholder="Nhập câu hỏi" value="" style="width:80%;">
                    <div class="question-action">
                        <button type="button" class="remove-question-item">Xóa câu hỏi</button>
                    </div>
                </div>
                <div class="form-row">
                    <label for="" class="control-label text-left">Câu trả lời</label>
                    <textarea name="question_answer[${questionIndex}][answer]" class="ck-editor" id="${textareaId}" placeholder="Nhập câu trả lời" style="width:100%; margin-top:10px;"></textarea>
                </div>
            </div>
        </div>`
        $('.ibox-ques .program-content').append(html)
        CKEDITOR.replace(textareaId);
    })
}

HT.removeQuestion= () => {
    $(document).on('click', '.remove-question-item', function(){
        let _this = $(this)
        _this.parents('.question-wrapper').remove()
    })
}

HT.addTrain = () => {
    $(document).on('click', '.add-train', function(){

        let trainIndex = $('.train-wrapper').length

        let html = `<div class="ibox mt20 train-wrapper" data-train-index=${trainIndex}>
            <div class="ibox-title">
                <div class="uk-flex uk-flex-middle uk-flex-space-between">
                    <input type="text" name="training_major[${trainIndex}][train_name]" class="form-control" placeholder="Nhập vào hệ đào tạo" value="" style="width:75%;">
                    <div class="train-action">
                        <button type="button" class="add-major-item mr10">+ Thêm ngành đào tạo</button>
                        <button type="button" class="remove-train-item">Xóa hệ đào tạo</button>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
            
            </div>
        </div>`

        $('.ibox-train .program-content').append(html)
            
    })
    
}

HT.removeTrain = () => {
    $(document).on('click', '.remove-train-item', function(){
        let _this = $(this)
        _this.parents('.train-wrapper').remove()
    })
}


HT.addMajorItem = () => {
    $(document).on('click', '.add-major-item', function(){
        let _this = $(this)
        let trainWrapper = _this.parents('.train-wrapper')
        let trainIndex = trainWrapper.data('train-index')
        let majorIndex = trainWrapper.find('.major-item').length
        let majorItem = `<div class="major-item" data-major-index=${majorIndex}>
            <div class="row">
                <div class="col-lg-2">
                    <div class="train-content">
                        <div class="title mb10">
                            <input type="text" class="form-control" name="training_major[${trainIndex}][major][${majorIndex}][code]" placeholder="Nhập vào mã ngành" value="" > 
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="train-info">
                        <div class="mb10">
                            <input type="text" class="form-control" name=training_major[${trainIndex}][major][${majorIndex}][name] placeholder="Nhập vào tên ngành" style="width:100% !important;">
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="train-info">
                        <div class="mb10">
                            <input type="text" class="form-control" name=training_major[${trainIndex}][major][${majorIndex}][grade] placeholder="Nhập vào xếp loại" style="width:100% !important;">
                        </div>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="train-info">
                        <div class="mb10">
                            <input type="text" class="form-control" name=training_major[${trainIndex}][major][${majorIndex}][rank] placeholder="Nhập vào xếp hạng" style="width:100% !important;">
                        </div>
                    </div>
                </div>
                <div class="col-lg-1">
                    <button type="button" class="form-control btn btn-danger remove-major">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>`
        _this.parents('.train-wrapper').find('.ibox-content').append(majorItem)
    })
}

HT.removeMajor = () => {
    $(document).on('click', '.remove-major', function(){
        let _this = $(this)
        _this.parents('.major-item').remove()
    }) 
}





$(document).ready(function(){
    HT.addMajorItem()
    HT.removeMajor()
    HT.addTrain()
    HT.removeTrain()
    HT.addQuestion()
    HT.removeQuestion()
    HT.addProgram()
    HT.removeProgram()
    HT.exportExcel()
    HT.changeOrder()
    HT.approve()
    HT.switchery()
    HT.select2()
    HT.changeStatus()
    HT.checkAll()
    HT.checkBoxItem()
    HT.allChecked()
    HT.changeStatusAll()
    HT.sortui()
    HT.int()
    HT.setupDatepicker()
    HT.setupDateRangePicker()
    HT.sort()
    HT.changeFieldStatus()
    
});


const addCommas = (nStr) => { 
    nStr = String(nStr);
    nStr = nStr.replace(/\./gi, "");
    let str ='';
    for (let i = nStr.length; i > 0; i -= 3){
        let a = ( (i-3) < 0 ) ? 0 : (i-3);
        str= nStr.slice(a,i) + '.' + str;
    }
    str= str.slice(0,str.length-1);
    return str;
}
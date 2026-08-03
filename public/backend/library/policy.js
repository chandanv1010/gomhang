(function($) {
	"use strict";
	var HT = {}; 

    HT.addProgram = () => {
        $(document).on('click', '.add-policy', function(){

            let chapterIndex = $('.policy-wrapper').length

            let textareaId = `ckPolicy_${chapterIndex}`; 

            let html = `<div class="ibox mt20 policy-wrapper" data-chapter-index=${chapterIndex}>
                <div class="ibox-title">
                    <div class="uk-flex uk-flex-middle uk-flex-space-between mb15">
                        <input type="text" name="scholarship_policy[${chapterIndex}][title]" class="form-control" placeholder="Nhập vào tên chính sách" value="" style="width:75%;">
                        <div class="chapter-action">
                            <button type="button" class="remove-policy-item">Xóa chính sách</button>
                        </div>
                    </div>
                    <div class="form-row">
                        <label for="" class="control-label text-left">Nội dung</label>
                        <textarea name="scholarship_policy[${chapterIndex}][description]" data-height="200" class="ck-editor" id="${textareaId}" placeholder="Nhập mô tả chính sách" style="width:100%; margin-top:10px;"></textarea>
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


	$(document).ready(function(){
        HT.addProgram()
        HT.removeProgram()
	});

})(jQuery);


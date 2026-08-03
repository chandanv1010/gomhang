(function($) {
	"use strict";
	var HT = {}; // Khai báo là 1 đối tượng
	var timer;
    var _token = $('meta[name="csrf-token"]').attr('content');

    // HT.review = () => {

    //     var modal = UIkit.modal("#review");

    //     $(document).on('click', '.btn-send-review', function(e){

    //         e.preventDefault();

    //          let fileInput = $('.review-image').get(0);

    //         let file = null;

    //         if (imageFile && imageFile.length > 0) {

    //             const file = imageFile[0];
    //             const allowedTypes = ['image/png', 'image/jpeg', 'image/webp']; // Sửa từ 'image/jpg' thành 'image/jpeg'
    //             const maxSize = 5 * 1024 * 1024; // 5MB
                
    //             if (!allowedTypes.includes(file.type)) {
    //                 alert('Vui lòng chọn tệp có định dạng PNG, JPG, WebP hoặc SVG.');
    //                 return false;
    //             }
                
    //             if (file.size > maxSize) {
    //                 alert('Hình ảnh quá lớn. Vui lòng chọn tệp dưới 5MB.');
    //                 return false;
    //             }
                
    //             console.log('File hợp lệ:', file.name);
    //         } else {
    //             alert('Vui lòng chọn một file.');
    //             return false;
    //         }

    //         let option = {
    //             score: $('.rate:checked').val(),
    //         }

    //         if(typeof option.score == 'undefined'){
    //             alert('Bạn chưa chọn điểm đánh giá')
    //             return false
    //         }

    //         let formData = new FormData();
    //         formData.append('score', $('.rate:checked').val());
    //         formData.append('image', file); 
    //         formData.append('description', $('.review-textarea').val());
    //         formData.append('gender', $('.gender:checked').val());
    //         formData.append('fullname', $('.product-preview input[name=fullname]').val());
    //         formData.append('email', $('.product-preview input[name=email]').val());
    //         formData.append('phone', $('.product-preview input[name=phone]').val());
    //         formData.append('reviewable_type', $('.reviewable_type').val());
    //         formData.append('reviewable_id', $('.reviewable_id').val());
    //         formData.append('_token', _token);
    //         formData.append('parent_id', $('.review_parent_id').val());

    //         $.ajax({
	// 			url: 'ajax/review/create', 
	// 			type: 'POST', 
	// 			data: formData,
    //             processData: false, 
    //             contentType: false, 
	// 			dataType: 'json', 
	// 			beforeSend: function() {
					
	// 			},
	// 			success: function(res) {
	// 				if(res.code === 10){
    //                     toastr.success(res.messages, 'Thông báo từ hệ thống!')
    //                     modal.hide()
    //                     location.reload()

    //                  }else{
    //                     toastr.error(res.messages, 'Thông báo từ hệ thống!')
    //                  }
	// 			},
	// 		});
    //     })
    // }
    HT.review = () => {
	
        $(document).on('click', '.btn-send-review', function(e){
            e.preventDefault();
            
            let fileInput = $('.review-image').get(0);
            
            if (fileInput.files && fileInput.files.length > 0) {

                let selectedFile = fileInput.files[0];
                
                const allowedTypes = ['image/png', 'image/jpeg', 'image/webp'];

                const maxSize = 5 * 1024 * 1024;
                
                if (!allowedTypes.includes(selectedFile.type)) {
                    alert('Vui lòng chọn tệp có định dạng PNG, JPEG hoặc WebP.');
                    return false;
                }
                
                if (selectedFile.size > maxSize) {
                    alert('Hình ảnh quá lớn. Vui lòng chọn tệp dưới 5MB.');
                    return false;
                }
                
                let formData = new FormData();

                formData.append('image', selectedFile); 
                formData.append('score', $('.rate:checked').val());
                formData.append('description', $('.review-textarea').val());
                formData.append('gender', $('.gender:checked').val());
                formData.append('fullname', $('.product-preview input[name=fullname]').val());
                formData.append('email', $('.product-preview input[name=email]').val());
                formData.append('phone', $('.product-preview input[name=phone]').val());
                formData.append('reviewable_type', $('.reviewable_type').val());
                formData.append('reviewable_id', $('.reviewable_id').val());
                formData.append('_token', _token);
                formData.append('parent_id', $('.review_parent_id').val());

                $.ajax({
                    url: 'ajax/review/create', 
                    type: 'POST',
                    data: formData,
                    processData: false, 
                    contentType: false, 
                    success: function(response) {
                        console.log('Success:', response);
                        alert('Gửi Đánh Giá Thành Công!');
                        location.reload()
                    },
                    error: function(xhr, status, error) {
                        console.log('Error:', error);
                    }
                });
                
            } else {
                alert('Vui lòng chọn một file.');
                return false;
            }
        });

    }

	$(document).ready(function(){
		HT.review()
	});

})(jQuery);


// menu show //
$(function(){
	$('#navigation li').on('mouseenter', function(){
		$('ul', this).stop(true, false).slideDown(300);
	});
});

// menu hide //
$(function(){
	$('#navigation li').on('mouseleave', function(){
		$('ul', this).stop(true, false).slideUp(300);
	});
});

/* file upload */
$(function(){
	$('input[type="file"]', this).on('change', function(){
		var input_file = $(this);
		$('.in-file-text').html(input_file.val());
		// if($('.file-name').length > 0) {
			// $('.upload_image').css('display', 'block');
		// }
	})
})
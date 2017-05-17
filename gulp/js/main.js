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

/* switch */
$(function() {
	$('.notification').on('click', function() {
		var notification = $(this).attr('id');
		var status = $(this).prop('checked');
		var senddata = 'notification='+escape(notification)+'&status='+escape(status);
		$.ajaxSetup({cache: false}); 
		$.ajax({
			url: "/notification/set",
			data: senddata,
			type: "POST",
			dataType: "json",
			success: function(data){

			}
		})
	})
})

$(document).ready(function() {
	  var sum = $('.text_signature > input').val().length;
	  $('#numsymbols').html(sum);

	$( ".text_signature > input" ).keyup(function() {
	  var sum = $(this).val().length;
	  $('#numsymbols').html(sum);
	});
})
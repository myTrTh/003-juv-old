// add vote options
$(function(){
	$('#add_option').on('click', function(){
		var count = $('.optionsgroup > input').length;
		var div = $('.optionsgroup');
		div.append('<input type="text" name="option[' + count + ']">');
	})
})

// delete vote options
$(function(){
	$('#remove_option').on('click', function(){
		var count = $('.optionsgroup > input').length;

		if(count > 2) {
			var last_input = $('.optionsgroup input:last');
			last_input.remove();
		} else {
			alert("Должно быть минимум два варианта ответа");
		}
	})
})
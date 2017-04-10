/* show and hide users panel */
$(function(){
	$('.users-panel-info').on('click', function(){
		var pr = $(this).parent();
		$('.users-panel-body', pr).slideToggle(300);
	})
})

/* set users role */
$(function(){
	$('.send_group').on('click', function(){
		var form = $(this).parent();
		var old = form.children('input[type="hidden"]').val();
		var rowid = form.attr('id');
		var id = rowid.substr(4);
		var role = form.children('input:checked').val();

		var data = "id=" + escape(id) + "&role=" + escape(role) + "&old=" + escape(old);
		$.ajaxSetup({cache: false});
		$.ajax({
			url: '/ajax/adminpanel/setrole',
			type: 'POST',
			data: data,
			// dataType: 'json',
			success: function() {
				form.children('input[type="hidden"]').val(role);
				if(role == 'ROLE_BANNED_0') {
					$('#panel' + id).removeClass('background-warning');
					$('#panel' + id).removeClass('background-danger');
				} else if (role == 'ROLE_BANNED_1') {
					$('#panel' + id).removeClass('background-danger');
					$('#panel' + id).addClass('background-warning')
				} else {
					$('#panel' + id).removeClass('background-warning');
					$('#panel' + id).addClass('background-danger')
				}

			}
		});

	})
})
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
					$('#panel' + id).removeClass('background-danger')
				} else {
					$('#panel' + id).addClass('background-danger')
				}

			}
		});

	})
})

$(document).ready(function() {
	var val = $("input:checked").val();
	if(val == 1) {
		$(".schema1").css("display", "block");
		$(".schema2").css("display", "block");
		$(".schema3").css("display", "none");
	} else if (val == 2) {
		$(".schema1").css("display", "block");
		$(".schema2").css("display", "none");
		$(".schema3").css("display", "none");
	} else {
		$(".schema1").css("display", "block");
		$(".schema2").css("display", "block");
		$(".schema3").css("display", "block");
	}
})

$(function() {
	$(document).on("focus", ".radios", function() {
		var val = $(this).val();

	if(val == 1) {
		$(".schema1").show(500);
		$(".schema2").show(500);
		$(".schema3").hide(500);
	} else if (val == 2) {
		$(".schema1").show(500);
		$(".schema2").hide(500);
		$(".schema3").hide(500);
	} else {
		$(".schema1").show(500);
		$(".schema2").show(500);
		$(".schema3").show(500);
	}
	})
})

// change users //
$(document).ready(function() {
	var howChanges = $('.tournament-user div').length;
	$('.allow').html(howChanges);
})

// add users for tournament //
$(function() {
	$(document).on('click', '.preteam', function() {
		var element = $(this);
		var rowid = element.attr("id");
		var id = rowid.substr(1);
		element.removeClass("preteam");
		element.addClass("team");

		var schema = $('#schema').html();
		var how = $('.tournament-user div').length;

		if(schema == 1) {
			var users = $(".tournament-user");
			users.append(element);
		} else if (schema == 2) {
			var users = $(".tournament-user");
			users.append(element);

			if((how % 2) == 0)
				users.append(" - ");
			else
				users.append("<br>");

		} else if (schema == 3) {
			var usergroups = $("#groups").html();
			var users = $(".tournament-user");
			
			if(how % usergroups == 0) {
				var group;
				if(how == 0)
					group = "A";
				if(how == 4)
					group = "B";
				if(how == 8)
					group = "C";
				if(how == 12)
					group = "D";
				if(how == 16)
					group = "E";
				if(how == 20)
					group = "F";
				if(how == 24)
					group = "G";
				if(how == 26)
					group = "H";
				users.append("<br>ГРУППА " + group + "<br>");
			}
			
			users.append(element);

		}

		var input = $('.form-hidden');
		input.append('<input type="hidden" id="form_user_' + id + '" name="form[user][' + id + ']" value="' + id + '" class="form-control" />');

		var endhow = $('.tournament-user div').length;
		$('.allow').html(endhow);
		var all = $('.all').html();
		var button = $('.button');

		if($('.groupoff').html() == 0) {
			if(all == endhow)
				button.show();
			else
				button.hide();
		} else {
			if(endhow == 2 || endhow == 4 || endhow == 8 || endhow == 16 || endhow == 32 || endhow == 64 || endhow == 128 || endhow == 256)
				button.show();
			else
				button.hide();			
		}
	});
})
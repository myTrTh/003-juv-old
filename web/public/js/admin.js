/* datetime picker */
var flatpickr_option = 	{
    	enableTime: true,
    	minDate: "today",
    	"locale": {
    		"firstDayOfWeek": 1
    	},
    	time_24hr: true
    };


$(function() {
	// new Flatpickr(document.querySelectorAll('input[id^=dtp]'), flatpickr_option);
	$("input[id^=dtp]").flatpickr(flatpickr_option);
})


/* show and hide users panel */
$(function(){
	$('.users-panel-info').on('click', function(){
		var pr = $(this).parent();
		$('.users-panel-body', pr).slideToggle(300);
	})
})

/* switch access tournament */
$(function() {
	$('.user-access').on('click', function() {
		var rowuser = $(this).attr('id');
		var user = rowuser.substr(3);
		var st = rowuser.substr(0, 3);
		var status = $(this).prop('checked');
		var senddata = 'user='+escape(user)+'&status='+escape(status)+'&st='+escape(st);
		$.ajaxSetup({cache: false}); 
		$.ajax({
			url: "/access/access",
			data: senddata,
			type: "POST",
			dataType: "json",
			success: function(data){

			}
		})
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
			url: '/ajax/adminpanel/setban',
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
		console.log(val);
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
		var button = $('.but');
		if($('#groups').html() == 0) {
			if(all == endhow) {
				console.log('all ' + all + ' allow ' + endhow);
				button.show();
			}
			else {
				button.hide();
			}
		} else {
			if(endhow == 2 || endhow == 4 || endhow == 8 || endhow == 16 || endhow == 32 || endhow == 64 || endhow == 128 || endhow == 256)
				button.show();
			else
				button.hide();			
		}
	});
})

/* add score */
$(function(){
	$('#add_score').on('click', function(event){
		event.stopPropagation();
		var block = $('.scoregroup');
		var inputblock = $('.score').last();
		var count = $('.scoregroup > .score').length;		
		var newblock = inputblock.clone();
		$('input', newblock).prop('id', 'dtpbefore' + count);
		newblock.appendTo(block);
		newblock.find("input[id^=dtp]").flatpickr(flatpickr_option);
	})
})


// delete vote options
$(function(){
	$('#remove_score').on('click', function(){
		var count = $('.scoregroup > .score').length;

		if(count > 1) {
			var last_input = $('.score:last');
			last_input.remove();
		} else {
			alert("Должен быть минимум один матч");
		}
	})
})

$( function(){
    $('.select-text').click( function(){
        $(this).select()
    })
})
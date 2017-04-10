// show smile
$(function(){
	$('.headsmile').on('click', function(){
		$('.smilepanel').slideToggle(200);
	})
})

// bb code
$(function(){
	$('.bbimg').on('click', function(){
		
		// var ttbody = $(this).parent().parent();
		var bbtag = $(this).attr('id');
		var textbody = $('textarea');

		// Набор bb кодов
		if(bbtag == 'post') {
			var tag_start = 'post:';
			var tag_end = '';
		} else {
			var tag_start = '[' + bbtag + ']';
			var tag_end = '[/' + bbtag + ']';
		}


		var start = textbody[0].selectionStart;
		var end = textbody[0].selectionEnd;
		var alltext = textbody.val();
		var needtext = textbody.val().substr(start, end - start);
		var bb_and_text = tag_start + needtext + tag_end;

		var cursor_position = textbody.val().length;

		var start_text = alltext.substr(0, start);
		var end_text = alltext.substr(end, cursor_position);
		textbody.val(start_text + bb_and_text + end_text);
		var newlength = textbody.val().length;
		textbody.focus();
		if(needtext.length == 0){
			textbody[0].setSelectionRange(start+tag_start.length, start+tag_start.length);			
		} else {
			textbody[0].setSelectionRange(start, end+tag_start.length+tag_end.length);
		}

	});
})

/* add smiles */
$(function(){
	$('.smiles').on('click', function(){
		
		var smile = $(this).attr('id');
		var textbody = $('textarea');

		// Набор bb кодов
		var tag_start = smile;
		var tag_end = '';
		console.log(tag_start);

		var start = textbody[0].selectionStart;
		var end = textbody[0].selectionEnd;
		var alltext = textbody.val();
		var needtext = textbody.val().substr(start, end - start);
		var bb_and_text = tag_start + needtext + tag_end;

		var cursor_position = textbody.val().length;

		var start_text = alltext.substr(0, start);
		var end_text = alltext.substr(end, cursor_position);
		textbody.val(start_text + bb_and_text + end_text);
		var newlength = textbody.val().length;
		textbody.focus();
		if(needtext.length == 0){
			textbody[0].setSelectionRange(start+tag_start.length, start+tag_start.length);			
		} else {
			textbody[0].setSelectionRange(start, end+tag_start.length+tag_end.length);
		}

	});
})

// spoiler
$(function(){
	$('.spoiler-name').on('click', function(){
		var spoiler = $(this).parent();
		$('.spoiler-body:first', spoiler).slideToggle(200);

		var sign = $('.sign', spoiler).html();
		if(sign == '+')
			$('.sign:first', spoiler).html('−');
		else
			$('.sign:first', spoiler).html('+');		
	});
})

// up down //
$(function(){
	$('.tumbler').on('click', function(){
		var row = $(this).attr('id');
		var sign = row.substr(0, 1);
		var id = row.substr(1);

		$('#u' + id).html('');
		$('#d' + id).html('');

		var senddata = 'id='+escape(id)+'&sign='+escape(sign);
		$.ajaxSetup({cache: false}); 
		$.ajax({
			url: "/guestbook/ranking",
			data: senddata,
			type: "POST",
			dataType: "json",
			success: function(data){
				if(data.sum == 0)
					$('#r' + id).html("<span class='gray'>" + data.sum + "</span>");
				else if(data.sum > 0)
					$('#r' + id).html("<span class='green'>+" + data.sum + "</span>");
				else if(data.sum < 0)
					$('#r' + id).html("<span class='red'>" + data.sum + "</span>");

				if(data.author_sum == 0)
					$('.us' + data.author_id).html("<span class='gray'>" + data.author_sum + "</span>");
				else if(data.author_sum > 0)
					$('.us' + data.author_id).html("<span class='green'>+" + data.author_sum + "</span>");
				else if(data.author_sum < 0)
					$('.us' + data.author_id).html("<span class='red'>" + data.author_sum + "</span>");									
					
				$('#ru' + id).html(data.plus);
				$('#rd' + id).html(data.minus);
			}
		})
	})
})

// show rank //
$(function(){
	$('.post-ranking').on('mouseenter mouseleave', function(){
		var parent = $(this).parent();
		$('.rank-users', parent).stop(true, false).slideToggle(400);
	})
})

// nach //
$(function(){
	$('div[id^="nach"]').on('mouseenter', function(e){
		e.preventDefault();
		$('.toolkit', this).stop(true, false).slideDown(200);
	})
})

$(function(){
	$('div[id^="nach"]').on('mouseleave', function(e){
		e.preventDefault();
		$('.toolkit', this).stop(true, false).slideUp(200);
	})
})

// top nach //
$(function(){
	$('div[id^="topnach"]').on('mouseenter', function(e){
		e.preventDefault();
		$('.toptoolkit', this).stop(true, false).fadeIn(200);
	})
})

$(function(){
	$('div[id^="topnach"]').on('mouseleave', function(e){
		e.preventDefault();
		$('.toptoolkit', this).stop(true, false).fadeOut(200);
	})
})

// edit start
$(function() {
	$('.edit-post').on('click', function() {

		var idpost = $(this).attr('id');
		var id = idpost.substr(4);
		var textarea = $('#message' + id);

		var oldmessage = textarea.html().trim();
		var rowmessage = $('#hm' + id).text().trim();
		$('#hm' + id).html(oldmessage);

		var height = textarea.outerHeight(true) + 5;

		if(height < 150)
			height = 150;

		textarea.html("<textarea style='height: " + height + "px'>" + rowmessage + "</textarea><button id='update" + id + "' class='update button button-main'>Обновить</button><button id='cancel" + id + "' class='cancel button button-main'>Отмена</button>");
	})
})

// edit cancel
$(function(){
	$(document).on('click', '.cancel', function(){
		var rowid = $(this).attr('id');
		var id = rowid.substr(6);
		var message = $('#hm' + id).html().trim();
		var old = $('#message' + id + ' > textarea').html().trim();

		$('#hm' + id).html(old);
		$('#message' + id).html(message);
	})
})

// edit push //
$(function() {
	$(document).on('click', '.update', function() {
		var rowid = $(this).attr('id');
		var id = rowid.substr(6);
		var post = $('#post' + id);
		var editedtext = $('#message' + id + ' > textarea').val();
		var senddata = 'id='+escape(id)+'&message='+encodeURIComponent(editedtext);
		$.ajaxSetup({cache: false}); 
		$.ajax({
			url: "/guestbook/edit",
			data: senddata,
			type: "POST",
			dataType: "json",
			success: function(data){
				if(data['error'] == 1) {
					alert(data['text']);
				} else {
					$('#message' + id).html(data['text']);
					$('#hm' + id).html(data['hide']);
				}
			}
		})
	})
})

// quote
$(function(){
	$('.quote').on('click', function(){
		var quoteid = $(this).parent().parent().parent().parent().attr('id');
		var id = quoteid.substr(4);
		var user = $(this).parent().parent().prev().children().next().children().children().html().trim();
		var date = $('#hd' + id).text().trim();
		var message = $('#hm' + id).text().trim();
		console.log(message);
		var quote_text = '[quote author=' + user + ' date=' + date +' post=' + id + ']\n' + message + '\n[/quote]\n\n';
		var textarea = $('textarea');
		var start = textarea[0].selectionStart;
		var end = textarea[0].selectionEnd;
		var alltext = textarea.val();
		var start_text = alltext.substr(0, start);
		var cursor_position = textarea.val().length;
		var end_text = alltext.substr(end, cursor_position);
		textarea.val(start_text + quote_text + end_text);
		textarea.focus();
		textarea[0].setSelectionRange(start+quote_text.length, start+quote_text.length);

		$("html, body").animate({ scrollTop: 220 }, 500);
	});
})
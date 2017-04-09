/* quote */
$(function() {
	$('.quote').on('click', function() {
		
		var post = $(this).parent().parent();

		var idpost = post.attr('id');
		var id = idpost.substr(4);

		var user = $('.post-author', post).text().trim();
		var date = $('#hd' + id).text().trim();
		var message = $('.post-info-message', post).text().trim();		

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

		$("html, body").animate({ scrollTop: 0 }, 500);
	});
});

// edit start
$(function() {
	$('.edit').on('click', function() {
		var post = $(this).parent().parent();

		var idpost = post.attr('id');
		var id = idpost.substr(4);

		var textarea = $('.post-info-message', post);

		var oldmessage = textarea.html().trim();
		var rowmessage = $('#hm' + id).text().trim();
		$('#hm' + id).html(oldmessage);

		var height = textarea.outerHeight(true) + 5;

		if(height < 150)
			height = 150;

		textarea.html("<textarea style='width: 100%; height: " + height + "px'>" + rowmessage + "</textarea><br><button id='update" + id + "' class='update'>Обновить</button><button id='cancel" + id + "' class='cancel'>Отмена</button>");
	})
})

// edit cancel
$(function(){
	$(document).on('click', '.cancel', function(){
		var rowid = $(this).attr('id');
		var id = rowid.substr(6);
		var post = $('#post' + id);
		var message = $('#hm' + id).html().trim();
		var old = $('.post-info-message > textarea', post).html();

		$('#hm' + id).html(old);
		$('.post-info-message', post).html(message);
	})
})

// edit push //
$(function() {
	$(document).on('click', '.update', function() {
		var rowid = $(this).attr('id');
		var id = rowid.substr(6);
		var post = $('#post' + id);
		var editedtext = $('.post-info-message > textarea', post).val();
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
					$('.post-info-message', post).html(data['text']);
					$('#hm' + id).html(data['hide']);
				}
			}
		})
	})
})

// bb code
$(function(){
	$('.bbimg').on('click', function(){
		
		var ttbody = $(this).parent().parent();
		var bbtag = $(this).attr('id');
		var textbody = $('textarea');

		// Набор bb кодов
		if(bbtag == 'bold'){
			var tag_start = '[b]';
			var tag_end = '[/b]';
		} else if(bbtag == 'cursive'){
			var tag_start = '[i]';
			var tag_end = '[/i]';
		} else if(bbtag == 'strike'){
			var tag_start = '[s]';
			var tag_end = '[/s]';
		} else if(bbtag == 'underline'){
			var tag_start = '[u]';
			var tag_end = '[/u]';
		} else if(bbtag == 'post'){
			var tag_start = '[post]';
			var tag_end = '[/post]';
		} else if(bbtag == 'quote'){
			var tag_start = '[quote]';
			var tag_end = '[/quote]';
		} else if(bbtag == 'spoiler'){
			var tag_start = '[spoiler]';
			var tag_end = '[/spoiler]';
		} else if(bbtag == 'center'){
			var tag_start = '[center]';
			var tag_end = '[/center]';
		} else if(bbtag == 'left'){
			var tag_start = '[left]';
			var tag_end = '[/left]';
		} else if(bbtag == 'right'){
			var tag_start = '[right]';
			var tag_end = '[/right]';
		} else if(bbtag == 'h1'){
			var tag_start = '[h1]';
			var tag_end = '[/h1]';
		} else if(bbtag == 'h2'){
			var tag_start = '[h2]';
			var tag_end = '[/h2]';
		} else if(bbtag == 'red'){
			var tag_start = '[red]';
			var tag_end = '[/red]';
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
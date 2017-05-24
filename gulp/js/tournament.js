/* switch access tournament */
$(function() {
	$('.tournament-access').on('click', function() {
		var rowuser = $(this).attr('id');
		var user = rowuser.substr(1);
		var st = rowuser.substr(0, 1);
		var status = $(this).prop('checked');
		var tr = $('#trid').html();
		var senddata = 'user='+escape(user)+'&status='+escape(status)+'&tr='+escape(tr)+'&st='+escape(st);
		$.ajaxSetup({cache: false}); 
		$.ajax({
			url: "/access/set",
			data: senddata,
			type: "POST",
			dataType: "json",
			success: function(data){

			}
		})
	})
})

$(function(){
	$('.player-scored').on("click", function(){

		var steps = $("#howscored");
		var howw = steps.text();

		scored = ["/public/images/scored/empty.png", "/public/images/scored/goal.png", "/public/images/scored/assist.png", "/public/images/scored/yellow.png", "/public/images/scored/red.png"];
		var img_src = $('img', this).attr('src');

		active = ['empty', 'goal', 'assist', 'yellow', 'red'];

		// ЕСЛИ КОЛ-ВО ДЕЙСТВИЙ МЕНЬШЕ 7, ДОБАВЛЯЕМ НОВЫЕ ДЕЙСТВИЯ
		if(howw <= 7) {

			if(howw == 7 && img_src == "/public/images/scored/empty.png") {
				alert("Можно выбрать только 7 действий!");
			} else {

				for(i=0;i<scored.length;i++) {
					if(img_src == scored[i])
						y = i;
				}

				if(y == 4)
					x = 0;
				else
					x = y+1;

				$('img', this).attr("src", scored[x]);
				$('input', this).attr("value", active[x]);

				var how = $('.player-scored > input[value!="empty"]').length;
				steps.text(how);
			}


		}

	})
})

$(function(){
	$('.player-keeper').on("click", function(){

		scored = ["/public/images/scored/emptykeeper.png", "/public/images/scored/null.png", "/public/images/scored/one.png", "/public/images/scored/two.png", "/public/images/scored/three.png"];
		
		var img_src = $('img', this).attr('src');
		active = ['emptykeeper', 'null', 'one', 'two', 'three'];

		for(i=0;i<scored.length;i++) {
			if(img_src == scored[i])
				y = i;
		}

		if(y == 4)
			x = 0;
		else
	 		x = y+1;

		$('img', this).attr("src", scored[x]);
		$('input', this).attr("value", active[x]);		
	})
})
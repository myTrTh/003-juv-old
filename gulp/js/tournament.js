/* switch access tournament */
$(function() {
	$('.tournament-access').on('click', function() {
		var rowuser = $(this).attr('id');
		var user = rowuser.substr(4);
		var status = $(this).prop('checked');
		var tr = $('#trid').html();
		console.log(status);
		var senddata = 'user='+escape(user)+'&status='+escape(status)+'&tr='+escape(tr);
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
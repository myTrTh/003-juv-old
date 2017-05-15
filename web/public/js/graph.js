// GRAPH
$(document).ready(function() {
	var id1 = $("#tool1").html();
	var id2 = $("#tool2").html();
	var id3 = $("#tool3").html();
    var tr = $("#tr").html();

	if(id1 != 0 || id2 != 0 || id3 != 0) {
		var senddata = 'user1='+escape(id1)+'&user2='+escape(id2)+'&user3='+escape(id3)+'&tr='+escape(tr);
		$.ajax({
            url: "/graph/get",
			data: senddata,
			type: "POST",
			dataType: "json",
			success: function(allinformation){
                if(allinformation['error'] == 1) {
                    $('#errorsview').html("График не может быть выведен.");
                }

                console.log(allinformation);
			// 	for(i=0; i<3; i++) {
			// 		if(allinformation.logins[i] == undefined)
			// 			allinformation.logins[i] = " ";
			// 	}
   //                 	Highcharts.setOptions({
   //                 		colors: ['#058DC7', '#FF9655', '#00CC33']
   //                 	});
   //                  $('#bodytools').highcharts({
   //                      title: {
   //                          text: allinformation.name,
   //                          x: -20 //center
   //                      },
   //                      xAxis: {
   //                          categories: allinformation.tours
   //                      },
   //                      yAxis: {
   //                      	categories: allinformation.positions,
   //                      	reversed: true,
   //                      	tickInterval: 1,
   //                      	min: 1,
   //                      	max: allinformation.users,
   //                          title: {
   //                              text: ''
   //                          },
   //                          plotLines: [{
   //                              value: 0,
   //                              width: 1,
   //                              color: '#808080'
   //                          }]
   //                      },
   //                      tooltip: {
   //                          valueSuffix: ' место'
   //                      },
   //                      legend: {
   //                          layout: 'vertical',
   //                          align: 'right',
   //                          verticalAlign: 'middle',
   //                          borderWidth: 0
   //                      },
   //                      series: [
   //                      {
   //                          name: allinformation.logins[0],
   //                          data: allinformation.line[0]
   //                      }, 
   //                      {
   //                          name: allinformation.logins[1],
   //                          data: allinformation.line[1]
   //                      }, 
   //                      {
   //                          name: allinformation.logins[2],
   //                          data: allinformation.line[2]
   //                      }]
   //                  });
            }
        });
    };
});
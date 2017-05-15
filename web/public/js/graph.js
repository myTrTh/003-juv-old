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
      success: function(all){

        console.log(all.graph.pos[9])

        if(all['error'] == 1) {
          $('#errorsview').html("График не может быть выведен.");
        }

        Highcharts.setOptions({
          colors: ['#058DC7', '#FF9655', '#00CC33']
        });
        $('#bodytools').highcharts({
          title: {
            text: ""
          },
          xAxis: {
            categories: all.graph.pos.tours
          },          
          yAxis: {
            categories: 20,
            reversed: true,
            tickInterval: 1,
            min: 1,
            max: all.graph.ynum,
            title: {
              text: ''
            },
            plotLines: [{
              value: 0,
              width: 1,
              color: '#808080'
            }]
            },
            tooltip: {
              valueSuffix: ' место'
            },
            legend: {
              layout: 'vertical',
              align: 'right',
              verticalAlign: 'middle',
              borderWidth: 0
            },
            plotOptions: {
              series: {
                pointStart: 1
              }
            },
            series: [
            {
              name: 'Slava',
              data: all.graph.pos[9]
            },
            {
              name: 'bello',
              data: all.graph.pos[4]
            },            
            ]
            // series: [
            // {
            //   name: all.body.pos,
            //   data: all.body.score
            // }, 
            // {
            //   name: allinformation.logins[1],
            //   data: allinformation.line[1]
            // }, 
            // {
            //   name: allinformation.logins[2],
            //   data: allinformation.line[2]
            // }]
            });
            }
        });
    };
});
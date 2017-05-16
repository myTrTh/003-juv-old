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
        if(all['error'] == 1) {
          $('#errorsview').html("График не может быть выведен.");
        }

        Highcharts.setOptions({
          colors: ['#058DC7', '#FF9655', '#00CC33']
        });

        $('#bodytools').highcharts({

          title: {
              text: ''
          },

            tooltip: {
                valueSuffix: ' место'
            },
          xAxis: {
            categories: 4,
            tickInterval: 1,
            min: 1,
            max: all.graph.tours          
          },

          yAxis: {
              title: {
                  text: ''
              },
              categories: 20,
              reversed: true,
              tickInterval: 1,
              min: 1,
              max: all.graph.users,              
          },
          legend: {
              layout: 'vertical',
              align: 'right',
              verticalAlign: 'middle'
          },

          plotOptions: {
              series: {
                  pointStart: 1
              }
          },

          series: all.graph.series

        });
      }
    });
  };
});
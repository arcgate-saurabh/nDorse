<script type="text/javascript">
$(function () {
   
    $('#container').highcharts({
        chart: {
            type: 'column'
        },
		exporting: {
         enabled: false
},
credits: {
         enabled: false
},
        title: {
            text: 'nDorsement History By day'
        },
        xAxis: {
            type: 'category',
			
			
        },
        yAxis: {
            title: {
                text: 'nDorsement',
				style: {
                    
                    font: 'bold 10px sans-serif'
                }
            }

        },
        legend: {
            enabled: true
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true,
                    format: '{point.y:f}'
                }
            }
        },


         series: [<?php echo  $data;?>]
    });
});
		</script>

<!--<div id="container" style="min-width: 310px; height: 300px; max-width: 500px; margin: 0 auto"></div>-->
<div id="container" style="width:100%; margin: 0 auto"></div>

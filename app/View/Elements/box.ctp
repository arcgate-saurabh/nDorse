<?php $title = "nDorsement by ".$type;
if(in_array(strtolower($type),array("sub org","job title"))){
	$title = "nDorsement History by ".$type;
}?>
<script type="text/javascript">
$(function () {
    $('#container').highcharts({
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
		exporting: {
         enabled: false
},
credits: {
         enabled: false
},
        title: {
            text: '<?php echo $title;?>'
        },
        tooltip: {
			enabled: false
            
        },
        plotOptions: {
            pie: {
                allowPointSelect: false,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
					distance: 8,
                    format: '{point.y}',
					
					style: {
                        fontSize: "14px"
                    }
                    
                },
				showInLegend: true
            }
			
        },
		  legend: {
            enabled: true,
            
	
	itemStyle: {
                fontSize: '12px',
                fontWeight: 'bold'
            },
            useHTML: true,
            labelFormatter: function() {
                return '<div style="text-align: left; width:200px;">' + this.name + '</div>';
            }
        },
        series: [<?php echo  $data;?>]
    });
});
		</script>
<?php $min_width =350;
if($min_width>$width){
	$min_width =$width;
}
?>
<div id="container" style="width:100%; margin: 0 auto"></div>

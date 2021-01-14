<?php

if($counter == "" && $startofweekarray == ""){
    $counter = "no_data";
}

if(isset($zoomingfeature)){
    $id = "containerzooming2";
}else{
    $id = "container2";
}
?>
<script type="text/javascript">
    //=============to check if data is blank or not and series accordingly
    var series = [];
    var chart_data = [];
    var date_array = []
    var idData = [];
    if ('<?php echo $counter?>' == 'no_data') {
        chart_data = '<?php echo $counter;?>';
    } else {
        chart_data = <?php echo $counter;?>;
        series = chart_data['counter'];
        date_array = chart_data['date_array'];
        idData = $.parseJSON('<?php echo $idData; ?>');
    }

var fseries = new Array();
    var startofweek = new Array();
    var seriesdata = new Array();
    var data = {}
    if (series != "no_data") {
        for (tmp in series) {
            var id = series[tmp]['id'];
//            delete series[tmp]['id'];
            fseries.push({"maxPointWidth": 20, "name": tmp, 'data': series[tmp], 'id' : idData[tmp]});
        }
        for (tmp in date_array) {
            startofweek.push("Week Of " + date_array[tmp]);
        }
    }

    //========rendering the data of x and y axis

    $(function () {
        var id = '<?php echo $id;?>';
        $('#' + id).highcharts({
            chart: {
                type: 'column',
                marginBottom: 160,
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'Weekly nDorsement Hisory By Department'
            },
            xAxis: {
                categories: startofweek,
                title: {
                    text: 'Event Date'
                },
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'nDorsements'
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
            },
            legend: {
                align: 'left',
                layout: 'horizontal',
                verticalAlign: 'bottom',
                x: 60,
                floating: true,
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
                borderColor: '#CCC',
                borderWidth: 1,
                shadow: false
            },
            plotOptions: {
                column: {
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true,
                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
                        style: {
                            textShadow: '0 0 3px black'
                        },
                        formatter: function () {
//                            console.log(this);
                            var val = this.y;
                            if (val <= 0) {
                                return '';
                            }
                            return val;
                        }
                    }
                },
                series: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function (event) {
                                 var id = event.point.series.userOptions.id;
                                 var week = this.category;
                                 week = week.replace("Week Of ", "");
                                 window.location.href = siteurl + 'organizations/weekHistory/' + id + "/" + week;
                            }
                        }
                    }
                }
            },
            series: fseries


        });
    });

</script>
<?php if($id == "container2"){
    echo '<div id="'.$id.'" style="min-width: 350px; height:338px; max-width:100%; margin: 0 auto"></div>';
}else{
    echo '<div id="'.$id.'" style="min-width: 1100px; height:500px; max-width:100px; margin: 0 auto"></div>';
}?>
<!--<div id="container2" style="min-width: 350px; height:300px; max-width:500px; margin: 0 auto"></div>-->



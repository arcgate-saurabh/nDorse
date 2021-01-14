<?php
$series = "";

if (!empty($endorsementbyday)) {
    $seriesdata = "";
    foreach ($endorsementbyday as $lval) {
        if ($seriesdata == "") {
            $seriesdata = "{
                  name: '" . $this->Time->Format($lval[0]["cdate"], DATEFORMAT) . "',
                 y: " . $lval[0]["cnt"] . "}";
        } else {
            $seriesdata .= ",{
                   name: '" . $this->Time->Format($lval[0]["cdate"], DATEFORMAT) . "',
                 y: " . $lval[0]["cnt"] . "}";
        }
    }

//                echo $seriesdata;exit;
    $series = "  {
                    name: 'Date',
                    colorByPoint: false,
                    data: [" . $seriesdata . "]}";
    //echo $seriesdata;
}

$data = $series;
$div = "nDorsement History By Day";

if (isset($zoomingfeature)) {
    $id = "containerzooming";
} else {
    $id = "container";
}
?>
<script type="text/javascript">
    $(function () {
        var id = '<?php echo $id; ?>';
        $('#' + id).highcharts({
            chart: {
                type: 'column'
            },
            exporting: {
                enabled: true
            },
            credits: {
                enabled: false
            },
            title: {
                text: '<?php echo $div; ?>'
            },
            xAxis: {
                type: 'category'
            },
            yAxis: {
                title: {
                    text: 'nDorsement'
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
                        //format: '{point.y:.1f}'
                    },
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function () {
                                window.location.href = siteurl + 'organizations/dayHistory/' + this.name;
                            }
                        }
                    }
                }
            },

            series: [<?php echo $data; ?>]
        });
    });
</script>

<!--<div id="container" style="min-width: 310px; height: 300px; max-width: 500px; margin: 0 auto"></div>-->
<?php
if ($id == "container") {
    $styleparameter = "min-width: 310px; height:340px; max-width:1000px; margin: 0 auto";
    if (isset($chartfor) && $chartfor == "client") {
        $styleparameter = "min-width: 470px; height:510px; max-width:1000px; margin: 0 auto";
    }
    echo '<div id="' . $id . '" style="' . $styleparameter . '"></div>';
} else {
    echo '<div id="' . $id . '" style="min-width: 1100px; height:500px; max-width:10000px; margin: 0 auto"></div>';
}
?>


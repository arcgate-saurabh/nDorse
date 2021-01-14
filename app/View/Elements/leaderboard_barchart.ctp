<?php
$series = $series1 = "";

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

if (!empty($endorsementbyWeek)) {
    $seriesdata = "";
    foreach ($endorsementbyWeek as $lval) {
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
    $series1 = "  {
                    name: 'Date',
                    colorByPoint: false,
                    data: [" . $seriesdata . "]}";
    //echo $seriesdata;
}
$data1 = $series1;
//pr($data1); exit;
?>
<div class="leaderBoardReports">
    <div class="row">
        <div class="col-sm-6">
            <h3>nDorsement History By Day(Current Month)</h3>
            <div class="bs-example" style="padding:20px 15px 5px 5px;">
                <div id="historyDay" style="height: 300px;"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <h3>nDorsement History By Week(Current Month)</h3>
            <div class="bs-example" style="padding:20px 15px 5px 5px;">
                <div id="historyWeek" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    Highcharts.chart('historyDay', {
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        legend: {
            enabled: false
        },
        xAxis: {
            type: 'category'
        },
        exporting: {
            enabled: false
        },
        yAxis: {
            title: {
                text: 'nDorsement'
            }

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
        credits: {enabled: false},
        series: [<?php echo $data; ?>]
    });

    Highcharts.chart('historyWeek', {
        chart: {
            type: 'column'
        },
        title: {
            text: ''
        },
        xAxis: {
            type: 'category'
        },
        legend: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
        yAxis: {
            title: {
                text: 'nDorsement'
            }

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
        credits: {enabled: false},
        series: [<?php echo $data1; ?>]
    });
    
</script>
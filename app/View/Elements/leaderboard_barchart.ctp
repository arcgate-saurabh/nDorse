<?php
$series = $series1 = "";
echo $totalActiveUsers;
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
            <h3>nDorsement History By Day</h3>
            <div class="bs-example" style="padding:20px 15px 5px 5px;">
                <div id="historyDay" style="height: 300px;"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <h3>nDorsement History By Week</h3>
            <div class="bs-example" style="padding:20px 15px 5px 5px;">
                <div id="historyWeek" style="height: 300px;"></div>
            </div>
        </div>
    </div>

    <div class="row" style="margin-top:30px ">
        <div class="col-sm-12">
            <h3>Daily Active Users</h3>
            <div class="bs-example" style="padding:20px 15px 5px 5px; margin-bottom: 10px;">
                <div id="monthlyNdorseNew" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
//    var monthsNew = ["Apr-20", "May-20", "Jun-20", "Jul-20", "Aug-20", "Sep-20", "Oct-20", "Nov-20", "Dec-20", "Jan-21", "Feb-21", "Mar-21"];
//    var totalEndorsementsNew = [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4];
        var monthsNew = <?php echo $monthsnew; ?>;
        var totalEndorsementsNew = <?php echo $totalActiveUsers; ?>;

        renderGraph1();

        function renderGraph1() {
            chart1 = new Highcharts.chart('monthlyNdorseNew', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: '',
                },
                exporting: {
                    enabled: false
                },
                legend: {
                    enabled: false
                },
                credits: {enabled: false},
                series: [{
                        data: totalEndorsementsNew,
                        color: '#5cb85c',
                        dataLabels: {
                            enabled: true,
                            rotation: 0,
                            color: '#FFFFFF',
                            align: 'center',
                            format: '{point.y}', // one decimal
                            y: 30, // 10 pixels down from the top
                            style: {
                                fontSize: '12px',
                                fontFamily: 'arial, Verdana, sans-serif',
                            }
                        }
                    }],
                tooltip: {
                    formatter: function () {
                        return 'Active users in <b>' + this.x + '</b> is <b>' + this.y + '</b>';
                    }
                },
                xAxis: {
                    categories: monthsNew,
                    title: {
                        text: null
                    }
                },
                yAxis: {
                    title: {
                        text: null
                    }
                },
            });
        }

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

        Highcharts.chart('historyActiveUser', {
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
    });

</script>
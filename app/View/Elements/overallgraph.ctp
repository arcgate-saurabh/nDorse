<?php
//pr(($finalCoreValueData)); 
//exit;
?>
<!--<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>-->
<div class="leaderBoardReports">
    <div id="container1" class="container hidden"></div>
    <div id="container2" class="container hidden"></div>
    <div id="container3" class="container hidden"></div>
    <div id="container4" class="container hidden"></div>

    <div id="buttonrow">
        <button id="export-png">Export to PNG</button>
        <button id="export-pdf">Export to PDF</button>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-md-6">
                    <!--<h3>Total Monthly nDorsements</h3>-->
                </div>

                <div class="col-md-3 pull-right">
                    <div class="reportGraphType" style="margin-bottom: 10px;">
                        <select class="form-control select-graph-type" data-id="monthlyNdorse">
                            <option value="2">Bar Graph</option>
                            <option value="1">Line Graph</option>
                        </select>
                    </div>
                </div>
            </div>            
            <div class="bs-example" style="padding:20px 15px 5px 5px; margin-bottom: 10px;">
                <div id="monthlyNdorse" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-md-6">
                    <!--<h3>Total Monthly nDorsements per Sub-Center/Facility</h3>-->
                </div>
                <div class="col-md-3">
                    <select class="form-control" name="subcenter" id="subcenter">
                        <option value="0"><b>All Sub-Centers/Facilities</b></option>
                        <?php
                        foreach ($subCenterArray as $id => $subcenter) {
                            $selected = '';
                            if ($user_subcenterID == $id) {
                                $selected = 'selected="selected"';
                            }
                            ?>
                            <option <?php echo $selected; ?> value="<?php echo $subcenter; ?>"><?php echo $subcenter; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="reportGraphType" style="margin-bottom: 10px;">
                        <select class="form-control select-graph-type" data-id="subMonthlyNdorse">
                            <option value="2">Bar Graph</option>
                            <option value="1">Line Graph</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bs-example" style="padding:20px 15px 5px 5px; margin-bottom: 10px;">
                <div id="subMonthlyNdorse" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-sm-12">

            <div class="row">
                <div class="col-md-6">
                    <!--<h3>Core Values Trends</h3>-->
                </div>
                <div class="col-md-3 pull-right">
                    <div class="reportGraphType"  style="margin-bottom: 10px;">
                        <select class="form-control select-graph-type" data-id="coreTrends">
                            <option value="2">Bar Graph</option>
                            <option value="1">Line Graph</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="bs-example" style="padding:20px 15px 5px 5px; margin-bottom: 10px;">
                <div id="coreTrends" style="height: 300px;"></div>
            </div>
        </div>
    </div>
    <br/>
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-md-6">
                    <!--<h3>Hashtag Trends</h3>-->
                </div>
                <div class="col-md-3 pull-right">
                    <div class="reportGraphType" style="margin-bottom: 10px;">
                        <select class="form-control select-graph-type" data-id="hashtagTrends">
                            <option value="2">Bar Graph</option>
                            <option value="1">Line Graph</option>
                        </select>
                    </div>
                </div>            
            </div>
            <div class="bs-example" style="padding:20px 15px 5px 5px; margin-bottom: 10px;">
                <div id="hashtagTrends" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(document).ready(function () {

        var months = <?php echo $months; ?>;
        var totalEndorsements = <?php echo $totalEndorsements; ?>;
        var finalSubcenterData = <?php echo $finalSubcenterData; ?>;
        var finalCoreValueData = <?php echo $finalCoreValueData; ?>;
        var finalHashtagsData = <?php echo $finalHashtagsData; ?>;
//        console.log(months);
        var monthlyNdorse = 'column';
        var subMonthlyNdorse = 'column';
        var coreTrends = 'column';
        var hashtagTrends = 'column';
        renderGraph1();
        renderGraph2();
        renderGraph3();
        renderGraph4();




        var chart21 = "";
        var chart11 = "";

        $(document).on("change", ".select-graph-type", function () {
            var graphId = $(this).attr('data-id');
            var graphType = $(this).val();
            if (graphType == 1) {
                var graph = 'line';
            } else {
                var graph = 'column';
            }

            switch (graphId) {
                case 'monthlyNdorse':
                    monthlyNdorse = graph;
                    renderGraph1();
                    break;
                case 'subMonthlyNdorse':
                    subMonthlyNdorse = graph;
                    renderGraph2();
                    break;
                case 'coreTrends':
                    coreTrends = graph;
                    renderGraph3();
                    break;
                case 'hashtagTrends':
                    hashtagTrends = graph;
                    renderGraph4();
                    break;
                default:
                // code block
            }


        });

        function renderGraph1() {
            chart1 = new Highcharts.chart('monthlyNdorse', {
                chart: {
                    type: monthlyNdorse
                },
                title: {
                    text: 'Total Monthly nDorsements',
                },
                legend: {
                    enabled: false
                },
                exporting: {
                    buttons: {
                        contextButton: {
                            symbol: null,
                            menuItems: null,
                            text: 'Download',
                            theme: {
                                'stroke-width': 1,
                                stroke: '#e34e04',
                                fill: '#f47521',
                                r: 4,
                                padding: 8,
                                height: 15,
                                states: {
                                    hover: {
                                        stroke: '#1e245a',
                                        fill: '#1e245a'
                                    },
                                    select: {
                                        stroke: '#4cae4c',
                                        fill: '#a4edba'
                                    }
                                },
                                style: {
                                    color: '#ffffff',
                                    textDecoration: 'none',
                                },

                            },
                            onclick: function () {
                                this.exportChart({
                                    type: 'image/png'
                                });
                            }
                        }
                    }
                },
                credits: {enabled: false},
                series: [{
                        data: totalEndorsements,
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
                        return 'nDorsements in <b>' + this.x + '</b> is <b>' + this.y + '</b>';
                    }
                },
                xAxis: {
                    categories: months,
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
        function renderGraph2() {
            chart2 = new Highcharts.chart('subMonthlyNdorse', {
                chart: {
                    type: subMonthlyNdorse
                },
                title: {
                    text: 'Total Monthly nDorsements per Sub-Center/Facility'
                },
                xAxis: {
                    categories: months,
                    title: {
                        text: null
                    }
                },
//                tooltip: {
//                    formatter: function () {
//                        return 'nDorsements in <b>' + this.x + '</b> is <b>' + this.y + '</b>';
//                    }
//                },
                yAxis: {
                    min: 0,
                    title: {
                        text: '',
                        align: 'high'
                    },
                    labels: {
                        overflow: 'justify'
                    }
                },

                plotOptions: {
                    bar: {
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                legend: {
                    enabled: true
                },
                exporting: {
                    buttons: {
                        contextButton: {
                            symbol: null,
                            menuItems: null,
                            text: 'Download',
                            theme: {
                                'stroke-width': 1,
                                stroke: '#e34e04',
                                fill: '#f47521',
                                r: 4,
                                padding: 8,
                                height: 15,
                                states: {
                                    hover: {
                                        stroke: '#1e245a',
                                        fill: '#1e245a'
                                    },
                                    select: {
                                        stroke: '#4cae4c',
                                        fill: '#a4edba'
                                    }
                                },
                                style: {
                                    color: '#ffffff',
                                    textDecoration: 'none',
                                },

                            },
                            onclick: function () {
                                this.exportChart({
                                    type: 'image/png'
                                });
                            }
                        }
                    }
                },
                credits: {enabled: false},
                series: finalSubcenterData
            }, function (chart) {
                var $customLegend = $('#subcenter');

                $customLegend.change(function () {
                    $option = $(this).val();
                    var series = chart.get();
                    if ($option == 0) {
                        $.each(series.series, function (index, value) {
                            value.show();
                        });
                    } else {
                        $.each(series.series, function (index, value) {
                            value.hide();
                        });
                        serie = chart.get($option);
                        serie.show();
                    }

                });

            }

            );
        }


        function renderGraph3() {
            chart3 = new Highcharts.chart('coreTrends', {
                chart: {
                    type: coreTrends
                },
                title: {
                    text: 'Core Values Trends'
                },
                xAxis: {
                    categories: months
                },
                yAxis: {
                    title: false
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: false
                    }
                },
                exporting: {
                    buttons: {
                        contextButton: {
                            symbol: null,
                            menuItems: null,
                            text: 'Download',
                            theme: {
                                'stroke-width': 1,
                                stroke: '#e34e04',
                                fill: '#f47521',
                                r: 4,
                                padding: 8,
                                height: 15,
                                states: {
                                    hover: {
                                        stroke: '#1e245a',
                                        fill: '#1e245a'
                                    },
                                    select: {
                                        stroke: '#4cae4c',
                                        fill: '#a4edba'
                                    }
                                },
                                style: {
                                    color: '#ffffff',
                                    textDecoration: 'none',
                                },

                            },
                            onclick: function () {
                                this.exportChart({
                                    type: 'image/png'
                                });
                            }
                        }
                    }
                },
                credits: {enabled: false},
                series: finalCoreValueData
            });
        }
        function renderGraph4() {
            chart4 = new Highcharts.chart('hashtagTrends', {
                chart: {
                    type: hashtagTrends
                },
                title: {
                    text: 'Hashtag Trends'
                },
                xAxis: {
                    categories: months
                },
                yAxis: {
                    title: false
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: false
                    }
                },
//                exporting: {
//                    enabled: false
//                },
                exporting: {
                    buttons: {
                        contextButton: {
                            symbol: null,
                            menuItems: null,
                            text: 'Download',
                            theme: {
                                'stroke-width': 1,
                                stroke: '#e34e04',
                                fill: '#f47521',
                                r: 4,
                                padding: 8,
                                height: 15,
                                states: {
                                    hover: {
                                        stroke: '#1e245a',
                                        fill: '#1e245a'
                                    },
                                    select: {
                                        stroke: '#4cae4c',
                                        fill: '#a4edba'
                                    }
                                },
                                style: {
                                    color: '#ffffff',
                                    textDecoration: 'none',
                                },

                            },
                            onclick: function () {
                                this.exportChart({
                                    type: 'image/png'
                                });
                            }
                        }
                    }
                },
                credits: {enabled: false},
                series: finalHashtagsData
            });
        }

    });



    /**
     * Create a global getSVG method that takes an array of charts as an
     * argument
     */
    Highcharts.getSVG = function (charts) {
        var svgArr = [],
                top = 0,
                width =1000;
        var txt1 = '';
        var txt2 = '';

        Highcharts.each(charts, function (chart) {
            var svg = chart.getSVG(),
                    // Get width/height of SVG for export
                    svgWidth = +svg.match(
                            /^<svg[^>]*width\s*=\s*\"?(\d+)\"?[^>]*>/
                            )[1],
                    svgHeight = +svg.match(
                            /^<svg[^>]*height\s*=\s*\"?(\d+)\"?[^>]*>/
                            )[1];

            svg = svg.replace(
                    '<svg',
                    '<g transform="translate(0,' + top + ')" '
                    );
            svg = svg.replace('</svg>', '</g>');

            top += svgHeight;
            width = Math.max(width, svgWidth);

            svgArr.push(svg);
        });
        
 
            
        txt1 = '<svg width="350" height="75" viewBox="0 0 350 75"><rect x="10" y="80" width="300" height="40" style="fill: white; stroke:black;stroke-width:2"/><g style="overflow:hidden; font-size:14; font-family: Arial"></text><text x="20" y="95" style="fill: black">Demo 1 = </text></g><g style="overflow:hidden; font-size:14; font-family: Arial"></text><text x="20" y="112" style="fill: black">Test Case = </text></g></svg>';
        //txt2 = '<svg width="500" height="75" viewBox="0 0 500 75"><rect x="500" y="1005" width="200" height="30" style="fill: white; stroke:black; stroke - width:2"/><g style="overflow:hidden; font - size:14; font - family: Arial"></text><text x="505" y="1024" style="fill: black">&Oslash; Checklist = </text></g></svg>';
    var svg = txt1.replace(
                    '<svg',
                    '<g transform="translate(0,' + top + ')" '
                    );
            svg = svg.replace('</svg>', '</g>');    
    svgArr.push(txt1);
        //svgArr.push(txt2);

        return '<svg height="' + top + '" width="' + width +
                '" version="1.1" xmlns="http://www.w3.org/2000/svg">' +
                svgArr.join(' ') + '</svg>';
    };

    /**
     * Create a global exportCharts method that takes an array of charts as an
     * argument, and exporting options as the second argument
     */
    Highcharts.exportCharts = function (charts, options) {

        // Merge the options
        options = Highcharts.merge(Highcharts.getOptions().exporting, options);

        // Post to export server
        Highcharts.post(options.url, {
            filename: options.filename || 'chart',
            type: options.type,
            width: options.width,
            svg: Highcharts.getSVG(charts)
        });
    };

    var chart1 = Highcharts.chart('container1', {

        chart: {
            height: 200
        },

        title: {
            text: 'Total Monthly nDorsements'
        },

        credits: {
            enabled: false
        },

        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },

        series: [{
                data: [29.9, 71.5, 106.4, 129.2, 144.0, 176.0,
                    135.6, 148.5, 216.4, 194.1, 95.6, 54.4],
                showInLegend: false
            }],

        exporting: {
            enabled: false // hide button
        }

    });

    var chart2 = Highcharts.chart('container2', {

        chart: {
            type: 'column',
            height: 200
        },

        title: {
            text: 'Total Monthly nDorsements per Sub-Center/Facility'
        },

        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },

        series: [{
                data: [176.0, 135.6, 148.5, 216.4, 194.1, 95.6,
                    54.4, 29.9, 71.5, 106.4, 129.2, 144.0],
                colorByPoint: true,
                showInLegend: false
            }],

        exporting: {
            enabled: false // hide button
        }

    });
    var chart3 = Highcharts.chart('container3', {

        chart: {
            type: 'column',
            height: 200
        },

        title: {
            text: 'Core Values Trends'
        },

        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },

        series: [{
                data: [176.0, 135.6, 148.5, 216.4, 194.1, 95.6,
                    54.4, 29.9, 71.5, 106.4, 129.2, 144.0],
                colorByPoint: true,
                showInLegend: false
            }],

        exporting: {
            enabled: false // hide button
        }

    });
    var chart4 = Highcharts.chart('container4', {

        chart: {
            type: 'column',
            height: 200
        },

        title: {
            text: 'Hashtag Trends'
        },

        xAxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },

        series: [{
                data: [176.0, 135.6, 148.5, 216.4, 194.1, 95.6,
                    54.4, 29.9, 71.5, 106.4, 129.2, 144.0],
                colorByPoint: true,
                showInLegend: false
            }],

        exporting: {
            enabled: false // hide button
        }

    });

    $('#export-png').click(function () {
        Highcharts.exportCharts([chart1, chart2, chart3, chart4]);
    });

    $('#export-pdf').click(function () {
        Highcharts.exportCharts([chart1, chart2, chart3, chart4], {
            type: 'application/pdf'
        });
    });

</script>
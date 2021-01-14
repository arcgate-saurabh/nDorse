<div class="leaderBoardReports">
    <div class="row">
        <div class="col-sm-12">
            <h3>nDorsement By Department</h3>
            <div class="bs-example" style="padding:20px 15px 5px 5px;">
                <div id="pieChartDept" style="height: 300px;"></div>
            </div>
            <br/><br/>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <h3>nDorsement Received  by Job Title</h3>
            <div class="bs-example" style="padding:20px 15px 5px 5px;">
                <div id="pieChartRec" style="height: 300px;"></div>
            </div>
        </div>
        <div class="col-sm-6">
            <h3>nDorsement Sent  by Job Title</h3>
            <div class="bs-example" style="padding:20px 15px 5px 5px;">
                <div id="pieChartSent" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    Highcharts.chart('pieChartRec', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
        },
        exporting: {
            enabled: false
        },

        credits: {enabled: false},

        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [
                    {
                        name: 'Staff',
                        y: 69.48
                    }, {
                        name: 'Physician',
                        y: 2.21,
                    }, {
                        name: 'Nurse',
                        y: 3.59
                    }, {
                        name: 'Supervisor',
                        y: 3.87
                    }, {
                        name: 'Director',
                        y: 5.25
                    }, {
                        name: 'Job Title Not Provide',
                        y: 10.22
                    }]
            }]
    });

    Highcharts.chart('pieChartSent', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
        },
        exporting: {
            enabled: false
        },

        credits: {enabled: false},

        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [{
                        name: 'Chrome',
                        y: 61.41,
                        sliced: true,
                        selected: true
                    }, {
                        name: 'Internet Explorer',
                        y: 11.84
                    }, {
                        name: 'Firefox',
                        y: 10.85
                    }, {
                        name: 'Edge',
                        y: 4.67
                    }, {
                        name: 'Safari',
                        y: 4.18
                    }, {
                        name: 'Sogou Explorer',
                        y: 1.64
                    }, {
                        name: 'Opera',
                        y: 1.6
                    }, {
                        name: 'QQ',
                        y: 1.2
                    }, {
                        name: 'Other',
                        y: 2.61
                    }]
            }]
    });

    /**/

    Highcharts.chart('pieChartDept', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: ''
        },
        exporting: {
            enabled: false
        },

        credits: {enabled: false},

        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
                name: 'Brands',
                colorByPoint: true,
                data: [{
                        name: 'CBO',
                        y: 61.41,
                        sliced: true,
                        selected: true
                    }, {
                        name: 'Patien Access',
                        y: 11.84
                    }, {
                        name: 'Emergency Department',
                        y: 10.85
                    }, {
                        name: 'MSICU',
                        y: 4.67
                    }, {
                        name: 'Social Services',
                        y: 4.18
                    }, {
                        name: 'Infection Control',
                        y: 1.64
                    }, {
                        name: 'One Day Surgery',
                        y: 1.6
                    }, {
                        name: 'Nursing Adminiatration',
                        y: 1.2
                    }, {
                        name: 'Trauma Program',
                        y: 2.61
                    }]
            }]
    });
</script>
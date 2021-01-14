<?php
$series = "";
if (!empty($leaderboard)) {
    $seriesdata = "";
    foreach ($leaderboard as $lval) {
        if ($seriesdata == "") {
            $seriesdata = "{
                                id: '" . $lval["OrgDepartments"]["department_id"] . "',
                                name: '" . addslashes($lval["OrgDepartments"]["department"]) . "',
                                y: " . $lval[0]["cnt"] . "}";
        } else {
            $seriesdata .= ",{
                                id: '" . $lval["OrgDepartments"]["department_id"] . "',
                                name: '" . addslashes($lval["OrgDepartments"]["department"]) . "',
                                y: " . $lval[0]["cnt"] . "}";
        }
    }
    $series = "  {
                            name: 'organization',
                            colorByPoint: true,
                            data: [" . $seriesdata . "],
                            point:{
                              events:{
                                  click: function (event) {
                                      window.location.href = siteurl + 'organizations/deptHistory/' + this.id;
                                  }
                              }
                          }     
                            }";
}
$data = $series;

if (isset($zoomingfeature)) {
    $id = "containerzooming1";
} else {
    $id = "container1";
}
?>
<script type="text/javascript">
    var id = '<?php echo $id; ?>';
    $(function () {
        $('#' + id).highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            exporting: {
                enabled: true
            },
            credits: {
                enabled: false
            },
            title: {
                text: 'nDorsement History By Department'
            },
            tooltip: {
                enabled: false

            },
            plotOptions: {
                pie: {
                    size: '80%',
                    allowPointSelect: false,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        distance: 8,
                        format: '{point.y}',
                    },
                    showInLegend: true
                }
            },
            legend: {
                enabled: true,
                align: 'right',
                verticalAlign: 'top',
                layout: 'vertical',
                x: 0,
                y: 100,
                itemMarginTop: 3,
                itemMarginBottom: 3,
                itemStyle: {
                    fontSize: '12px',
                    fontWeight: 'bold'
                },
                useHTML: true,
                labelFormatter: function () {
                    var breakwordresultant = this.name.match(/.{1,30}/g).join("-<br/>");
                    return '<div style="text-align: left; width:200px;">' + breakwordresultant + '</div>';
                }
            },
            series: [<?php echo $data; ?>]
        });
    });
</script>
<?php
if ($id == "container1") {
    echo '<div id="' . $id . '" style="min-width: 350px; height:338px; max-width:99%; margin: 0 auto"></div>';
} else {
    echo '<div id="' . $id . '" style="min-width: 1100px; height:500px; max-width:1000px; margin: 0 auto"></div>';
}
?>
<!--<div id="container1" style="min-width: 350px; height:300px; max-width:500px; margin: 0 auto"></div>-->

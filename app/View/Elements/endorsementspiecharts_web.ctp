<?php
$seriesjbtitle = "";
$htmljbtitledata = "";
if (!empty($detailedjobtitlechart)) {

    foreach ($detailedjobtitlechart["data"] as $name => $yaxis) {
        if (isset($detailedjobtitlechart["jobtitles"][$name])) {
            $htmljbtitledata .= "{
                                id:'" . $name . "',
                                name:'" . $detailedjobtitlechart["jobtitles"][$name] . "',
                                y:" . $yaxis . ",
                            },";
        }
    }
}

$seriesjbtitle = "  {
                    name: 'jbendorsement',
                    colorByPoint: true,
                    data: [" . $htmljbtitledata . "],
                     point:{
                          events:{
                              click: function (event) {
                                  window.location.href = siteurl + 'organizations/titleHistory/' + this.id;
                              }
                          }
                      }
                }";

$dataarray = array("data" => $seriesjbtitle, "chartfor" => "jobtitle", "zoomchart" => "no");



$width = "200px";
$style = 'style="min-width: 350px; height: 338px; max-width: 99%; margin: 0 auto"';
$seriesdata = $dataarray["data"];
if ($dataarray["chartfor"] == "jobtitle" && $dataarray["zoomchart"] == "no") {
    $divid = "containerjobtitlezoomno";
    $title = 'nDorsement History By Title';
}
if ($dataarray["chartfor"] == "entity" && $dataarray["zoomchart"] == "no") {
    $divid = "containerentityzoomno";
    $title = 'nDorsement History By Sub Org';
}
if ($dataarray["chartfor"] == "jobtitle" && $dataarray["zoomchart"] == "yes") {
    $divid = "containerjobtitlezoomyes";
    $style = 'style="min-width: 1100px; height: 500px; max-width: 99%px; margin: 0 auto"';
    $title = 'nDorsement History By Title';
}
if ($dataarray["chartfor"] == "entity" && $dataarray["zoomchart"] == "yes") {
    $divid = "containerebtityzoomyes";
    $style = 'style="min-width: 1100px; height: 500px; max-width: 99%px; margin: 0 auto"';
    $title = 'nDorsement History By Sub Org';
}
//======charts for endorsers login
if ($dataarray["chartfor"] == "corevalueschart" && $dataarray["zoomchart"] == "no") {
    $divid = "containercv";
    $title = 'nDorsement By Core Values';
    $style = 'style="min-width: 510px; height: 510px; max-width: 99%; margin: 0 auto"';
    $width = "300px";
}
if ($dataarray["chartfor"] == "department" && $dataarray["zoomchart"] == "no") {
    $divid = "containercv";
    $title = 'nDorsement History By Departments';
    $style = 'style="min-width: 500px; height: 520px; max-width: 100%; margin: 0 auto"';
    $width = "300px";
}
?>
<script type="text/javascript">
    var breakwordresultant = "";
    $(function () {
        $(document).ready(function () {
            $('#' + '<?php echo $divid; ?>').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie',
                },
                title: {
                    text: '<?php echo $title; ?>'
                },
                exporting: {
                    enabled: true
                },
                tooltip: {
                    enabled: false

                },
                credits: {
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
                        fontWeight: 'bold',
                    },
                    useHTML: true,
                    labelFormatter: function () {
                        //=============breaking a long word after 30 characters
                        if (this.name.length > 0) {
                            breakwordresultant = this.name.match(/.{1,30}/g).join("-<br/>");
                        } else {
                            breakwordresultant = "Empty";
                        }
                        return '<div style="text-align: left; width:<?php echo $width; ?>;">' + breakwordresultant + '</div>';
                    }
                },
                series: [<?php echo $seriesdata; ?>]
            });
        });
    });
</script>
<div id="<?php echo $divid; ?>" <?php echo $style; ?>></div>




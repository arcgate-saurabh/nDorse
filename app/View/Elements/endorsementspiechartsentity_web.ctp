    <?php
$width = "200px";
$style = 'style="min-width: 420px; height: 550px; max-width: 99%; margin: 0 auto"';
$seriesdata = $dataarray["data"];

if($dataarray["chartfor"] == "jobtitle" ){
    $divid = "containerjobtitlezoomno";
    $title = 'nDorsement History By Title';
}
if($dataarray["chartfor"] == "entity" ){
    $divid = "containerentityzoomno";
    $title = 'nDorsement History By Sub Organization';
}

?>
<script type="text/javascript">
    var breakwordresultant = "";
    $(function () {
        $(document).ready(function () {
            $('#' + '<?php echo $divid;?>').highcharts({
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie',
                },
                title: {
                    text: '<?php echo $title;?>'
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
                        if(this.name.length > 0 ){
                            breakwordresultant = this.name.match(/.{1,30}/g).join("-<br/>");
                        }else{
                            breakwordresultant = "Empty";
                        }
                        return '<div style="text-align: left; width:<?php echo $width;?>;">' + breakwordresultant + '</div>';
                    }
                },
                series: [<?php echo $seriesdata;?>]
            });
        });
    });
</script>
<div id="<?php echo $divid; ?>" <?php echo $style;?>></div>




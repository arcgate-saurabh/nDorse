<?php
echo $this->Html->script("highcharts");
echo $this->Html->script("modules/exporting");
echo $this->Html->script("modules/no-data-to-display");

$data = array(
    "textcenter" => "Organization Info",
    "righttabs" => "1",
    "orgid" => $organization_id
);
$headerpage = ($authUser["role"] == 1) ? 'header' : 'headerorg';
if ($authUser["role"] == 2) {
    $data['auth_users'] = $authUser;
}
echo $this->Element($headerpage, array('data' => $data));
?>

<div class="row row-padding" id="usersdetail">
    <div class="col-md-6">
        <?php
//        pr($userdata); exit;
        $user_image = $userdata['User']['image'];
        if ($user_image == "") {
            echo '<span class="pull-left" style="margin-right:15px;">' . $this->Html->image('user.png', array('class' => "img-circle", 'width' => '61', 'height' => '61')) . '</span>';
        } else {
            if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image)) {
                $user_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . $user_image;
                echo '<span class="pull-left" style="margin-right:15px;">' . $this->Html->image($user_imagenew, array("width" => "61", "height" => "61", "id" => "org_image", "class" => "img-circle")) . '</span>';
            } else {
                $user_imagenew = $this->Html->image('user.png', array('class' => "img-circle", 'width' => '61', 'height' => '61'));
                echo '<span class="pull-left" style="margin-right:15px;">' . $user_imagenew . '</span>';
            }
        }
        ?>
        <h6 class="user"><?php echo ucfirst($userdata['User']['fname']) . " " . ucfirst($userdata['User']['lname']); ?></h6>
        <h5 class="user-mail"><?php echo $userdata['User']['email']; ?></h5>

        <div class="col-md-12 col-xs-6">
            <div class="comp-name">
                <h4>Sub-Org : <?php echo $userdata['OrgSubcenter']['short_name']; ?></h4>
                <h4>Department : <?php echo $userdata['OrgDepartment']['name']; ?></h4>
                <h4>Job title : <?php echo $userdata['OrgJobtitle']['title']; ?></h4>
                <h4>Last login : <?php echo DATE('m-d-Y : h-i-s', strtotime($userdata["User"]["last_app_used"])); // $userdata['User']['last_app_used'];                       ?></h4>
                <b>Total Number of nDorsements : <?php echo $allvaluesendorsement; ?>
                    <br/>
                    Total nDorsements for Current Month : <?php echo $allvaluesendorsementMonthly; ?> </b>
                <br/> 
                <br/>


            </div>
        </div>
    </div>
    <div class="col-md-2 comp-name">
        <?php
        $orgname = $companydetail['name'];
        $orgid = $organization_id;
        echo '<h2>' . $this->Html->link($orgname, array('controller' => 'users', 'action' => 'editorg', $orgid));
//echo $this->Html->Image("edit_icon.png", array("data-toggle" => "tooltip", "title" => "Edit Organization", "class" => "editorgimage", "url" => array('controller'=>'users','action'=>'editorg',$orgid))).'</h2>';
        echo '<h3>' . $companydetail['shortname'] . '</h3>'
        ?>
        <p><?php
            echo $companydetail["street"];
            if ($companydetail["street"] != "" && $companydetail["city"] != "") {
                echo ", ";
            }
            ?> <?php echo $companydetail["city"]; ?></p>
        <p><?php
            echo $companydetail["state"];
            if ($companydetail["state"] != "" && $companydetail["zip"] != "") {
                echo ", ";
            }
            ?> <?php echo $companydetail["zip"]; ?></p>
    </div>
    <div id="buttonrow" style="margin-top: 20%;float: right;margin-right: 5%;">
        <button id="export-pdf">Download Summary</button>
    </div>
</div>



<!-- NEW CODE START FROM HERE-->
<?php
//pr(($finalCoreValueData)); 
//exit;
?>
<br/>
<br/>
<div class="leaderBoardReports">
    <div class="row">
        <div class="col-sm-12">
            <div class="row">
                <div class="col-md-6">
                    <h3>Monthly Received nDorsements (Last 12 Months)</h3>
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
                <div class="col-md-6"><h3>Core Values Trends</h3></div>
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
                    <h3>Hashtag Trends</h3>
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
        var finalCoreValueData = <?php echo $finalCoreValueData; ?>;
        var finalHashtagsData = <?php echo $finalHashtagsData; ?>;
//        console.log(months);
        var monthlyNdorse = 'column';
        var coreTrends = 'column';
        var hashtagTrends = 'column';

        renderGraph1();
        renderGraph3();
        renderGraph4();

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
                    text: ''
                },
                legend: {
                    enabled: false
                },
                exporting: {
                    enabled: false
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

        function renderGraph3() {
            chart3 = new Highcharts.chart('coreTrends', {
                chart: {
                    type: coreTrends
                },
                title: {
                    text: ''
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
                    enabled: false
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
                    text: ''
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
                    enabled: false
                },
                credits: {enabled: false},
                series: finalHashtagsData
            });
        }
        var chart1 = new Highcharts.chart('monthlyNdorse', {
            chart: {
                type: monthlyNdorse,
                width: 1000
            },
            title: {
                text: ''
            },
            legend: {
                enabled: false
            },
            exporting: {
                enabled: false
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
        var chart3 = new Highcharts.chart('coreTrends', {
            chart: {
                type: coreTrends,
                width: 1000
            },
            title: {
                text: ''
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
                enabled: false
            },
            credits: {enabled: false},
            series: finalCoreValueData
        });

        var chart4 = new Highcharts.chart('hashtagTrends', {
            chart: {
                type: hashtagTrends,
                width: 1000
            },
            title: {
                text: ''
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
                enabled: false
            },
            credits: {enabled: false},
            series: finalHashtagsData
        });
        Highcharts.getSVG = function (charts, texts, ) {
            var fullName = "<?php echo ucfirst($userdata['User']['fname']) . ' ' . ucfirst($userdata['User']['lname']); ?>";
            var orgName = "<?php echo $companydetail['name']; ?>";
            var subCenter = "<?php echo $userdata['OrgSubcenter']['short_name']; ?>";
            var department = "<?php echo $userdata['OrgDepartment']['name']; ?>";
            var jobtitle = "<?php echo $userdata['OrgJobtitle']['title']; ?>";
            var nDorsementRecv = "<?php echo $allvaluesendorsement; ?>";
            var lastLogin = "<?php echo DATE('m-d-Y : h-i-s', strtotime($userdata['User']['last_app_used'])); ?>";


            subCenter = (subCenter == '') ? "N/A" : subCenter;
            department = (department == '') ? "N/A" : department;
            jobtitle = (jobtitle == '') ? "N/A" : jobtitle;



            var svgArr = [],
                    top = 0,
                    topMargin = 260,
                    width = 0,
                    txt;
            Highcharts.each(charts, function (chart, i) {
                var svg = chart.getSVG();
                svg = svg.replace('<svg', '<g transform="translate(0,' + (top + topMargin) + ')" ');
                svg = svg.replace('</svg>', '</g>');
                if (i == 0) {
                    top += (chart.chartHeight + 300);
                } else {
                    top += (chart.chartHeight + 50);
                }



                width = Math.max(width, chart.chartWidth);
                if (i == 0) {
                    txt = texts[i];
                    txt = '<text x= "' + 0 + '" y = "' + (50) + '" style = "' + txt.attributes.style.value + '">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;User nDorsement Summary</text>';
                    txt += '<text x= "' + 0 + '" y = "' + (70) + '" style = "">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Name: ' + fullName + '</text>';
                    txt += '<text x= "' + 0 + '" y = "' + (90) + '" style = "">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Org: ' + orgName + '</text>';
                    txt += '<text x= "' + 0 + '" y = "' + (110) + '" style = "">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Sub-Center: ' + subCenter + '</text>';
                    txt += '<text x= "' + 0 + '" y = "' + (130) + '" style = "">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Department: ' + department + '</text>';
                    txt += '<text x= "' + 0 + '" y = "' + (150) + '" style = "">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Job Title: ' + jobtitle + '</text>';
                    txt += '<text x= "' + 0 + '" y = "' + (200) + '" style = "">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Total Number of nDorsements Received: ' + nDorsementRecv + '</text>';
                    txt += '<text x= "' + 0 + '" y = "' + (220) + '" style = "">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Last Login Date: ' + lastLogin + '</text>';
                    txt += '<text x= "' + 0 + '" y = "' + (250) + '" style = "font-weight: bold;"> &nbsp;&nbsp;&nbsp;&nbsp;Monthly Receive nDorsements (Last 12 Months)</text>';
                    svgArr.push(txt);
                }
                if (i == 0) {
                    txt = texts[i];
                    txt = '<text x= "' + 0 + '" y = "' + (top + 40) + '" style = "font-weight: bold;">  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Core Values Trends:</text>';
                    svgArr.push(txt);
                }
                if (i == 1) {
                    txt = texts[i];
                    txt = '<text x= "' + 0 + '" y = "' + (top + 40) + '" style = "font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hashtag Trends: </text>';
                    svgArr.push(txt);
                }

                svgArr.push(svg);
                topMargin = 60;
                top += 5;
            });
            return '<svg height="' + top + '" width="' + width + '" version="1.1" xmlns="http://www.w3.org/2000/svg">' + svgArr.join('') + '</svg>';
        };
        Highcharts.exportChartWithText = function (charts, texts, options) {
            options = Highcharts.merge(Highcharts.getOptions().exporting, options);
            Highcharts.post(options.url, {
                filename: options.filename || 'chart',
                type: options.type,
                width: options.width,
                svg: Highcharts.getSVG(charts, texts)
            });
        };
        var texts = $('.HC');
        $('#export-pdf').click(function () {
            Highcharts.exportChartWithText([chart1, chart3, chart4], texts, {
                //Highcharts.exportCharts([chart1, chart2, chart3, chart4], {
                type: 'application/pdf'
            });
        });

    });



</script>   
<!-- NEW CODE ENDED HERE-->




<!--<section>
    <div class="row"> 
        <button type="button" id="printFullList" class="btn btn-info btn-Preview-Image" rel="complete">Print all nDorsements</button>
    </div>
</section>-->
<section>
    <textarea name="message" class="HC hide" id="2" style="margin-left: 700px;font-size: 25px;font-weight: bold;">
  
    </textarea> 
    <div class="row">
        <div class="col-md-12" id="listingreportser">
            <div class="row">
                <div class="col-md-8">
                    <h3 style="color:#fff;">nDorsement Received: </h3>
                </div>
                <div class="col-md-4">
                    <div class="container-fluid"> 
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav navbar-right">
                                <?php /* <li><a href="#"><img src="<?php echo $this->webroot; ?>img/search_map-white.png" alt="" /></a></li> */ ?>
                                <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $this->webroot; ?>img/pancake-white.png" alt="" /> </a>
                                    <ul class="dropdown-menu">
                  <!--                    <li><a href="#"><?php // echo $this->Html->link(__('Save as Spreadsheet'), array('controller' => 'organizations', 'action' => 'export', '?' => array('orgid' => $organization_id, 'userid' => $user_id, 'information' => 'endorser')));                                   ?></a></li>-->
                                        <li><a href="javascript:void(0)" id="endorsementsreceivedsas" class="endorsementssas" data-userid = "<?php echo $user_id; ?>" data-information = "endorser">Save As Spreadsheet</a></li>
                                        <li><a href="javascript:void(0)" rel="listingreportser" class="btn-Preview-Image">Print</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <!-- /.navbar-collapse --> 
                    </div>
                </div>
            </div>
            <div data-example-id="striped-table" class="row bs-example">
                <div class="table-responsive scroll-header">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><div class="col-endor">nDorsed</div></th>
                                <th><div class="endor-date">nDorsement Date</div></th>
                                <th style="text-align: center"><div class="endor-date">Core Values</div></th>
                                <?php
                                foreach ($allothervalues["corevalues"] as $key => $corevaluesall) {
                                    echo '<th style="text-align:center;" class="iffyTip1" title="' . $corevaluesall["name"] . '">' . $corevaluesall["name"] . '</th>';
                                }
                                ?>
                                <th><div class="comment-div">Comments</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($allvaluesendorser)) { ?>
                                <tr>
                                    <td colspan="5">No Data Available</td>
                                </tr>
                                <?php
                            } else {
                                foreach ($allvaluesendorser as $endorservalues) {
                                    ?>
                                    <tr>
                                        <td ><?php echo $endorservalues["name"]; ?></td>
                                        <td><?php echo $this->Time->format($endorservalues["date"], DATEFORMAT); ?></td>
                                        <td style="text-align: center"><?php echo $endorservalues["totalpoints"]; ?></td>
                                        <?php
                                        foreach ($allothervalues["corevalues"] as $key => $corevaluesall) {
                                            if (in_array($key, $endorservalues["corevaluesid"])) {
                                                echo '<td style="text-align: center">' . $this->Html->Image("checked.png", array("alt" => "Checked")) . '</td>';
                                            } else {
                                                echo '<td style="text-align: center"></td>';
                                            }
                                        }
                                        ?>
                                        <td ><?php echo $endorservalues["endorsement_message"]; ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br>
    <br>
    <div class="row">
        <div class="col-md-12" id="listingreportseg">
            <div class="row">
                <div class="col-md-8">
                    <h3 style="color:#fff;">nDorsement Given:</h3>
                </div>
                <div class="col-md-4">
                    <?php
//                                echo $this->Html->link(__('Export'), array('controller' => 'organizations', 'action' => 'export', '?' => array('orgid' => $organization_id, 'userid' => $user_id, 'information' => 'endorsed')));
//                                
//                                echo $this->Html->image('fullview.png',array('class'=>"img-responsive full-view", "width" => 22, "height" => 22));
//                                echo $this->Html->image('search_map.png',array('class'=>"img-responsive full-view", "width" => 22, "height" => 22));
                    ?>
                    <div class="container-fluid"> 
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav navbar-right">
                                <?php /* <li><a href="#"><img src="<?php echo $this->webroot; ?>img/search_map-white.png" alt="" /></a></li> */ ?>
                                <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $this->webroot; ?>img/pancake-white.png" alt="" /> </a>
                                    <ul class="dropdown-menu">
                  <!--                    <li><a href="#"><?php echo $this->Html->link(__('Save as Spreadsheet'), array('controller' => 'organizations', 'action' => 'export', '?' => array('orgid' => $organization_id, 'userid' => $user_id, 'information' => 'endorsed'))); ?></a></li>-->
                                        <li><a href="javascript:void(0)" id="endorsementsreceivedsas" class="endorsementssas" data-userid = "<?php echo $user_id; ?>" data-information = "endorsed">Save As Spreadsheet</a></li>
                                        <li><a href="javascript:void(0)" rel="listingreportseg" class="btn-Preview-Image">Print</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <!-- /.navbar-collapse --> 
                    </div>
                </div>
            </div>
            <div data-example-id="striped-table" class="row bs-example">
                <div class="table-responsive scroll-header">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><div class="col-endor">nDorser</div></th>
                                <th><div class="endor-date">nDorsement Date</div></th>
                                <th style="text-align:center;"><div class="endor-date">Core Values</div></th>
                                <?php
                                foreach ($allothervalues["corevalues"] as $key => $corevaluesall) {
                                    //$tmp[] = $key;
                                    echo '<th style="text-align:center;" class="iffyTip1" title="' . $corevaluesall["name"] . '" >' . $corevaluesall["name"] . '</th>';
                                }
                                ?>
                                <th style="text-align:center;"><div class="comment-div">Comments</div></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($allvaluesendorsed)) {

                                foreach ($allvaluesendorsed as $endorsedvalues) {
                                    ?>
                                    <tr>
                                        <td><?php echo $endorsedvalues["name"]; ?></td>
                                        <td><?php echo $this->Time->format($endorsedvalues["date"], DATEFORMAT); ?></td>
                                        <td style="text-align:center;"><?php echo $endorsedvalues["totalpoints"]; ?></td>
                                        <?php
                                        foreach ($allothervalues["corevalues"] as $key => $corevaluesall) {
                                            if (in_array($key, $endorsedvalues["corevaluesid"])) {
                                                echo '<td style="text-align:center;">' . $this->Html->Image("checked.png", array("alt" => "Checked")) . '</td>';
                                            } else {
                                                echo '<td></td>';
                                            }
                                        }
                                        ?>
                                        <td ><?php echo $endorsedvalues["endorsed_message"]; ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr>
                                    <td colspan="5">No Data Available</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

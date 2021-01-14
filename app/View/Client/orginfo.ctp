<?php

echo $this->Html->script("highcharts");
    echo $this->Html->script("modules/exporting");
    echo $this->Html->script("modules/no-data-to-display");
    if(!empty($alldetailsorg)){
    $file_headers = @get_headers($alldetailsorg["org_image"]);
    $image = ($alldetailsorg["org_image"] != "" && $file_headers[0] != 'HTTP/1.1 404 Not Found') ? $alldetailsorg["org_image"]: Router::url('/', true)."img/comp_pic.png";    
?>
<div class="nDorsement-profile">
    <section>
        <div class="col-md-12">
            <div class="col-md-2 text-center"> 
                <img src="<?php echo $image; ?>" id="client_image" alt="" width="150px" height="100px"> 
            </div>
            <div class="col-md-6">
                <h2 class="u-name"><?php echo $alldetailsorg["org_name"];?></h2>
                <h3 class="u-profile"><?php echo $alldetailsorg["org_sname"];?></h3>
                <p><?php echo implode(", ", $alldetailsorg["streetcity"]);?></p>
                <p><?php echo implode(", ", $alldetailsorg["statecountry"]);?></p>
                <p><?php echo $alldetailsorg["zip"];?></p>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <a href="javascript:void(0);" class="btn btn-orange pull-right" data-toggle="modal" data-target="#myModalroleinorg">My Role in This Org</a>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </section>
</div>
<div class="nDorsement-profile">
    <section>
        <div class="col-md-12">
            <div class="col-md-4">
                <div class="nDorse-of-month">
                    <h4>Total nDorsements</h4>
                    <h3> <?php echo $alldetailsorg["org_totalendorsements"];?> </h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="nDorse-of-month">
                    <h4>Total Core Values</h4>
                    <h3> <?php echo $alldetailsorg["org_totalcv"];?> </h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="nDorse-of-month">
                    <h4>nDorsements For Current Month</h4>
                    <h3> <?php echo $alldetailsorg["org_totalendorsementsmonth"];?> </h3>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </section>
</div>
    <?php if(!empty($alldetailsorg["org_core_values"])){ ?>
<div class="nDorsement-profile">
    <section>
        <div class="core-values-div" style="margin-top:30px;">
            <table class="table table-hover table-core-value">
                <tbody>
                    <tr>
                        <th colspan="2"><strong>Core Values Fulfilled </strong></th>
                    </tr>
                    <?php foreach($alldetailsorg["org_core_values"] as $corevaluesfulfilled){
                        echo "<tr><td>".$corevaluesfulfilled["name"]."</td>";
                        echo "<td>".$corevaluesfulfilled["total"]."</td></td>";   
                    }?>
                </tbody>
            </table>
        </div>
    </section>
</div>
    <?php }?>


<div class="nDorsement-profile">
    <section style="padding:10px;">
        <?php echo $this->Form->Create("daterange");?>
        <div class="col-md-12 time-range">
            <div class="pull-left">
                <h3>Select a Time Range</h3>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="select-date col-md-12">
            <div class="col-md-3 form-group">
                <label> From</label>
                <input id="datepicker_startdate" name="startdate" type="text" readonly placeholder="Start Date" class="form-control date" value="<?php echo $startdate;?>">
            </div>
            <div class="col-md-3 form-group">
                <label> To</label>
                <input id="datepicker_enddate" name="enddate" readonly type="text" placeholder="End Date" class="form-control date" value="<?php echo $enddate;?>">
            </div>
            <div class="col-md-6 ">
                <button id="daterange" type="submit" class="btn btn-default">SEARCH</button>
                <button type="button" id="resetdates" class="btn btn-default">RESET</button>
            </div>
        </div>
        <?php echo $this->Form->End();?>
        <div class="clearfix"></div>

        <div class="panel panel-default">
            <?php 
                $seriesentity="  {
                    name: 'cvchart',
                    colorByPoint: true,
                    data: [".$graphbycorevalues."]}";
                $dataarray = array("chartfor" => "corevalueschart", "zoomchart" => "no", "data" => $seriesentity);
                echo $this->Element("endorsementspiecharts_web", array("dataarray" => $dataarray));
            
            ?>
        </div>
    </section>

</div>
<?php } ?>
  <?php //pr($orgdata["organization"]);?>
<div style="text-align: center"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>
<!--Join Org --> 
<div class="modal fade" id="myModalroleinorg" tabindex="-1" role="dialog" 
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"> 
            <!-- Modal Header -->

            <div class="modal-header">
                <button data-dismiss="modal" class="btn btn-default pull-right close" type="button">×</button>
                <h3>My Role in This Org</h3>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <form id="formusers" role="form">
                    <div class="col-md-12">
                        <div class="form-group">
                            <span>Sub Organization : </span>
                            <div class="select-style"> <?php echo $this->Form->input('entity_id', array('empty' => array(0 => 'Select Sub Organization'), 'label' => false, 'options' => $allexistinvalues["entities"], 'class' => 'form-control', "selected" => $optionsselected["entity_id"]));?> </div>
                        </div>
                        <div class="form-group">
                            <span>Department : </span>
                            <div class="select-style"> <?php echo $this->Form->input('department_id', array('empty' => array(0 => 'Select Department'), 'label' => false, 'options' => $allexistinvalues["departments"], 'class' => 'form-control', "selected" => $optionsselected["department_id"]));?> </div>
                        </div>
                        <div class="form-group">
                            <span>Job Title : </span>
                            <div class="select-style"> <?php echo $this->Form->input('job_title_id', array('empty' => array(0 => 'Select Job Title'), 'label' => false, 'options' => $allexistinvalues["jobtitles"], 'class' => 'form-control', "selected" => $optionsselected["job_title_id"]));?> </div>
                        </div>

                </form>
            </div>
            <div class="clearfix"></div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" id="saveroleinorg" data-orgid="<?php echo $org_id;?>" class="btn btn-orange-small pull-left"> Submit</button>
                <button type="button" class="btn btn-orange-small pull-left" data-dismiss="modal"> Skip </button>
            </div>
        </div>
    </div>
</div>

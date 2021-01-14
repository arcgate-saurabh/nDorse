<?php

echo $this->Html->script("highcharts");
    echo $this->Html->script("modules/exporting");
    echo $this->Html->script("modules/no-data-to-display");
	$current_org = $this->Session->read('Auth.User.current_org');
	?>

<div class="nDorsement-profile">
     <?php $orgdetail = array(
        "org_name" => $alldetailsorg["org_name"],
        "org_sname" => $alldetailsorg["org_sname"],
        "streetcity" => $alldetailsorg["streetcity"],
        "statecountry" => $alldetailsorg["statecountry"],
        "zip" => $alldetailsorg["zip"],
    );
    echo $this->Element("corgdetails", array("orgdata" => $orgdetail));
    ?>
  <div class="clearfix"></div>

    <section>
        <div class="col-md-12 time-range">
            <div class="pull-left">
                <h3>Select Date Range</h3>
            </div>
            <div class="pull-right">
                <button class="btn btn-default" id="showdataall">SHOW ALL</button>
            </div>
        </div>
        <div class="clearfix"></div>
    <?php echo $this->Form->Create("daterange",array("method"=>"post","id"=>"chartseachform"));?>
        <div class="select-date col-md-12">
            <div class="col-md-3 form-group">
                <label> From</label>
        <?php echo $this->Form->input('startdate', array('placeholder' => 'Start Date', 'type'=>'text','id' => 'datepicker_start', 'class' => 'form-control date', 'label' => false)); ?> </div>
            <div class="col-md-3 form-group">
                <label> To</label>
        <?php echo $this->Form->input('enddate', array('placeholder' => 'End Date', 'type'=>'text','id' => 'datepicker_end', 'class' => 'form-control date', 'label' => false)); ?> </div>
            <div class="col-md-6 ">
                <button type="button" class="btn btn-default" id="chartsearch">Apply</button>
            </div>
        </div>
    <?php echo $this->Form->End();?>
        <div class="clearfix"></div>
    </section>
    <section id="endorse-stats">
  <?php
			if($graphbycorevalues!=""){?>
        <div class="panel panel-default">
    <?php
           
			
               $seriesentity="  {
                    name: 'day',
                    colorByPoint: false,
                    data: [".$graphbycorevalues."]}";
               
                echo $this->Element("endorsementbyday_web", array("data" => $seriesentity, "chartfor" => "client"));
            
           ?>
        </div>
  <?php
			}else{
				 echo "<div class='nodataavailable'>No Data Available</div>"; 
			}
            ?>
</div>
</section>
</div>
</div>

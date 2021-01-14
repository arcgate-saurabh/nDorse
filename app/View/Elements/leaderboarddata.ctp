<?php

if(isset($zoomingfeature)){
    $divid = "tableleaderboardzoom";
}else{
    $divid = "tableleaderboard";
}
if($divid == "tableleaderboard"){
    echo '<div id="leaderboardprinting">';
}else{
    echo '<div id="leaderboardprintingzoom">';
}

?>
<div data-example-id="striped-table" class="row bs-example">
    <div id="<?php echo $divid?>">
        <div class="table-responsive" id="previewImage">
            <div class="scroll-header">
                <div class="leaderborad"> Leader Board </div>
                <a class="hidden" id="samplelink">downloadlink</a>
                <div class="clearfix"></div>
                <div data-html2canvas-ignore  class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <input onkeyup="searchleaderboard(this.value,<?php echo $organization_id?>)" type="text" name="searchleaderboard" id="searchleaderboard" placeholder="Filter This Report..." class="zooom">
                    <ul class="nav navbar-nav navbar-right">
                        <?php if($divid == "tableleaderboard"){?>
                        <li onclick="reportsandchartszoom(<?php echo $organization_id;?>, 'leader_board')"><a href="javascript:void(0)"><img src="<?php echo $this->webroot;?>img/fullview.png" alt="" /></a></li>
                        <?php }?>
                <!--<li><a href="#"><img src="<?php //echo $this->webroot; ?>img/search_map.png" alt="" /></a></li> -->
                        <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $this->webroot; ?>img/pancake.png" alt="" /> </a>
                            <ul class="dropdown-menu">
                                <!--<li><?php// echo $this->Html->Link("Save as Spreadsheet", array("controller" => "organizations", "action" => "export", '?' => array('orgid' => $organization_id, 'information' => 'leaderboard')), array('id' => 'savespreadsheetleaderboard'));?></li>-->
                                    <?php if($divid == "tableleaderboard"){
                                        echo '<li><a href="javascript:void(0)" id="saveasspreadsheetleaderboard">Save As SpreadSheet</a></li>';
                                        echo '<li><a href="javascript:void(0)" id="leaderboard" class="btn-Preview-Image">Print</a></li>';
                                    }else{
                                        echo '<li><a href="javascript:void(0)" id="leaderboardzoom" class="btn-Preview-Image">Print</a></li>';
                                    }?>

                            </ul>
                        </li>
                    </ul>
                </div>

                <!--        <div id="leaderboardsearchdiv" style="display: none">
                    <input type="text" name="searchleaderboard" id="searchleaderboard" placeholder="Search">
                </div>-->
<!--                <table class="table table-striped" id="headerhidden" style="display: none">
                    <thead>
                        <tr>
                            <th width="18%" >Name</th>
                            <th width="16%" align="center" style="text-align:center">Endorser</th>
                            <th width="16%" align="center" style="text-align:center">Endorsed</th>
                            <th width="16%" style="text-align:center">Total</th>
                            <th width="20%" style="text-align:center">Department</th>
                            <th width="14%" style="text-align:center; ">Sub Organization</th>
                        </tr>
                    </thead>
                </table>-->
            </div>
            <div class="scroll-body">
                <?php if(!empty($arrayendorsementdetail)){
                if($divid == "tableleaderboard"){
                    echo '<table id="leaderboardtable" class="table table-striped">';
                }else{
                    echo '<table id="leaderboardtablezoom" class="table table-striped">';
                }
                ?>
                
                    <thead id="scrollheader" style="background: rgba(0,0,0,0.15);">
                        <tr id="">
                            <th width="12%" class="headerSortDown" >Name</th>
                            <th width="7%" style="text-align: center;"><?php echo ENDORSER;?></th>
                            <th width="7%" style="text-align: center;">nDorsed</th>
                            <th width="7%" style="text-align: center;">Total</th>
                            <th width="14%">Department</th>
                            <th width="13%">Sub Organization</th>
                            <th width="15%">Title</th>
                            <th width="25%">Sub Center</th>
                        </tr>
                    </thead>
                    <tbody id="leaderboardsquare">
                        <?php 
                            //pr($arrayendorsementdetail); 
                          echo $this->Element("leaderboardsearching", array("arrayendorsementdetail" => $arrayendorsementdetail));  
                        ?>
                    </tbody>
                </table>
                <?php }else{
                    echo '<div class="no-data">No Data Available</div>';
                } ?>
            </div>
        </div>
    </div>
</div>
</div>

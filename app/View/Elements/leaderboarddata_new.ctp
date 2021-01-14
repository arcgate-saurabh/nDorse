
<div class="leaderBoardReports">
    
    <div class="row">
        <div class="col-sm-5">
            <h3>Leaderboard By Sub-Org</h3>
            <div class="bs-example">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="35%" class="headerSortDown botBrdr">Sub Organization</th>
                            <th width="30%" class="headerSortDown botBrdr"><?php echo ENDORSER;?></th>
                            <th width="30%" class="headerSortDown botBrdr">nDorsed</th>                            
                        </tr>
                    </thead>
                     <tbody class="scrollTbody" id="leaderboardsquare">
                        <?php 
                            //pr($arrayendorsementdetail); 
                          echo $this->Element("leaderboardsearching-new", array("arrayendorsementdetail" => $arrayendorsementdetail));  
                        ?>
                    </tbody>
                     <thead>
                        <tr>
                            <th width="35%" class="topBrdr">Total</th>
                            <th width="30%" class="topBrdr"><?php echo ENDORSER;?></th>
                            <th width="30%" class="topBrdr">nDorsed</th>                            
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
        <div class="col-sm-7">
                <h3>Leaderboard By Department</h3>
                <div class="bs-example flterLeaderBoard">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="30%" class="headerSortDown botBrdr">Department</th>
                                <th width="30%" class="headerSortDown botBrdr">Sub Org</th>
                                <th width="20%" class="headerSortDown botBrdr"><?php echo ENDORSER;?></th>
                                <th width="20%" class="headerSortDown botBrdr">nDorsed</th>                            
                            </tr>
                        </thead>
                         <tbody class="scrollTbody" id="leaderboardsquare">
                            <tr>
                              <td width="30%"><a href="/ndorselive_repo/organizations/listingreports/13946">A1 B1</a></td>
                              <td width="30%">0</td>
                              <td width="20%">0</td>
                              <td width="20%">0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
    <div class="flterLeaderBoard">
        <div class="row">
            
            <div class="col-sm-12">
                <h3>Leaderboard By Employee</h3>
                <div class="bs-example">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="20%" class="headerSortDown botBrdr">Name</th>
                                <th width="20%" class="headerSortDown botBrdr">Department</th>
                                <th width="20%" class="headerSortDown botBrdr">Sub-Org</th>
                                <th width="15%" class="headerSortDown botBrdr">Title</th>
                                <th width="15%" class="headerSortDown botBrdr"><?php echo ENDORSER;?></th>
                                <th width="20%" class="headerSortDown botBrdr">nDorsed</th>                            
                            </tr>
                        </thead>
                         <tbody class="scrollTbody" id="leaderboardsquare">
                            <tr>
                                <td width="20%" class="headerSortDown botBrdr">Name</td>
                                <td width="20%" class="headerSortDown botBrdr">Department</td>
                                <td width="20%" class="headerSortDown botBrdr">Sub-Org</td>
                                <td width="15%" class="headerSortDown botBrdr">Title</td>
                                <td width="15%" class="headerSortDown botBrdr"><?php echo ENDORSER;?></td>
                                <td width="20%" class="headerSortDown botBrdr">nDorsed</td>                            
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

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
<div data-example-id="striped-table" class="row bs-example" style="display: none;">
    <div id="<?php echo $divid?>">
        <div class="table-responsive" id="previewImage">
            <div class="scroll-header" style="position: relative;">
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
                    echo '<table id="leaderboardtable" class="table table-striped" style="width:120%; max-width:120%;">';
                }else{
                    echo '<table id="leaderboardtablezoom" class="table table-striped">';
                }
                ?>
                
                    <thead id="scrollheader">
                        <tr id="">
                            <th width="15%" class="headerSortDown" >Name</th>
                            <th width="14%" align="center"  style="text-align:center"><?php echo ENDORSER;?></th>
                            <th width="14%" align="center" style="text-align:center">nDorsed</th>
                            <th width="14%" style="text-align:center">Total</th>
                            <th width="20%" style="text-align:center">Department</th>
                            <th width="14%" style="text-align:center; ">Sub Organization</th>
                            <th width="14%" style="text-align:center; ">Title</th>
                            <th width="20%" style="text-align:center; ">Sub Center</th>
                        </tr>
                    </thead>
                    <tbody id="leaderboardsquare">
                        <?php 
                            //pr($arrayendorsementdetail); 
                          echo $this->Element("leaderboardsearching-new", array("arrayendorsementdetail" => $arrayendorsementdetail));  
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

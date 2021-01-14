<?php

//pr($alldata);?>
<!-- Leader Board -->

<div class="user-profile leader-board">
    <div  class="col-md-12">
        <ul id="tabs" class="nav nav-tabs">
            <li class="active"><a id="endorsed" href="#lb01" data-toggle="tab" aria-expanded="false">Top nDorsed</a></li>
            <li class=""><a id="endorser" href="#lb01" data-toggle="tab" aria-expanded="false">Top nDorser</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade in active col-md-12" id="lb01" style="border:1px solid #fff; color:#fff;">
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
                <section >
                    <div class="time-range">
                        <div class="pull-left">
                            <h3>Select a Time Range</h3>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="select-date col-md-12">
                        <div class="col-md-3 form-group">
                            <label> From</label>
                            <input id="datepicker_startdate" type="text" placeholder="Start Date" class="form-control date">
                        </div>
                        <div class="col-md-3 form-group">
                            <label> To</label>
                            <input id="datepicker_enddate" type="text" placeholder="End Date" class="form-control date">
                        </div>
                        <div class="col-md-6 ">
                            <button type="button" data-type="endorsed" id="setleaderboardendorseddata" class="btn btn-default">Apply</button>
                            <button type="button" id="resetdates" class="btn btn-default">RESET</button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </section>
        <?php if(!empty($alldata[0]["list"])){
            ?>
                <div class="col-md-12">
                    <table class="table table-hover leaderboardendorsedtable table-striped">
            <?php echo $this->Element("leaderboarddataclient", array("alldata" => $alldata,  "leaderboardtype" => "nDorsed"));?>
                    </table>
                </div>
        <?php }else{?>
                <section>
                    <div class="">
                        <table class="table table-hover leaderboardendorsedtable table-striped">
                            <tr class="table-head">
                                <td class="nodataavailable">No Data Available</td>
                            </tr>
                        </table>
                    </div>
                </section>
        <?php } ?>
                <div class="clearfix"></div>
            </div>

            <!-- other tab content--> 

        </div>
    </div>
    <div class="clearfix"></div>
    <div class="clearfix"></div>
</div>
<!-- Leader Board--> 
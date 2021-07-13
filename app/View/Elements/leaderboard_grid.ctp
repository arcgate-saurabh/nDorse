<div class="leaderBoardReports">
    <div class="row">
        <div class="col-sm-5">
            <h3>Leaderboard by Sub-Center/Facility</h3>
            <input type="hidden" id="selectedSubcenterId" class="selectedSubcenterId" value="0" />
            <input type="hidden" id="selectedDepartmentId" class="selectedDepartmentId" value="0" />
            <div class="bs-example">
                <table class="table table-striped" id="subCenterReport">
                    <thead>
                        <tr>
                            <th width="40%" class="headerSortDown botBrdr">Sub-Center/Facility</th>
                            <th width="30%" class="header botBrdr"><?php echo ENDORSER; ?></th>
                            <th width="30%" class="header botBrdr">nDorsed</th>                            
                        </tr>
                    </thead>
                    <tbody class="scrollTbody" id="leaderboardsquare">
                        <?php
                        //pr($subCenterArray);  exit;
                        //pr($subcenterNdorsementArray); exit;
                        echo $this->Element("leaderboardsearching-new", array('subCenterArray' => $subCenterArray, 'subcenterNdorsementArray' => $subcenterNdorsementArray));
                        ?>
                    </tbody>
                   <!--  <thead>
                        <tr>
                            <th width="35%" class="topBrdr">Total</th>
                            <th width="30%" class="topBrdr"><?php //echo ENDORSER; ?></th>
                            <th width="30%" class="topBrdr">nDorsed</th>                            
                        </tr>
                    </thead> -->
                </table>
            </div>
        </div>
        <div class="col-sm-7">
            <h3>Leaderboard by Department</h3>
            <div class="bs-example flterLeaderBoard">
                <table class="table table-striped" id="subCenterDeptReport">
                    <thead>
                        <tr>
                            <th width="30%" class="headerSortDown botBrdr">Department</th>
                            <th width="30%" class="header botBrdr">Sub-Center/Facility</th>
                            <th width="20%" class="header botBrdr"><?php echo ENDORSER; ?></th>
                            <th width="20%" class="header botBrdr">nDorsed</th>                            
                        </tr>
                    </thead>
                    <tbody class="scrollTbody" id="subcenterDepartment">
                        <?php
                        //pr($subcenterDepartmentArray); 
                        //exit;
                        echo $this->Element("orgsubcenter_dept_report", array('orgDepartments' => $orgDepartments, 'subcenterDepartmentArray' => $subcenterDepartmentArray, 'deptNdorsementCount' => $deptNdorsementCount));
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="flterLeaderBoard">
        <div class="row">

            <div class="col-sm-12">
                <h3>Leaderboard by Employee</h3>
                <div class="bs-example">
                    <table id="subCenterUserReport" class="table table-striped">
                        <thead>
                            <tr>
                                <th width="20%" class="headerSortDown botBrdr">Name</th>
                                <th width="20%" class="header botBrdr">Department</th>
                                <th width="20%" class="header botBrdr">Sub-Center/Facility</th>
                                <th width="15%" class="header botBrdr">Title</th>
                                <th width="15%" class="header botBrdr"><?php echo ENDORSER; ?></th>
                                <th width="20%" class="header botBrdr">nDorsed</th>                            
                            </tr>
                        </thead>
                        <tbody class="scrollTbody" id="subcenterUser">
                            <?php
//                            pr($orgAllUserDataArray); exit;
                            echo $this->Element("orgsubcenter_user_report", array('orgAllUserDataArray' => $orgAllUserDataArray, 'usersNdorsementsCounts' => $usersNdorsementsCounts, 'jobTitleIdArray' => $jobTitleIdArray, 'deptIDArray' => $deptIDArray, 'subcenterIdArray' => $subcenterIdArray));
                            ?>
                        </tbody>
                    </table>
                    <div class="col-md-offset-2 text-center col-md-10"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        $('#subCenterReport').tablesorter();
        $('#subCenterDeptReport').tablesorter();
        $('#subCenterUserReport').tablesorter();
        //selectedSubcenterId
        //selectedDepartmentId
        $(".subcenter_row").on("click", function () {
            var subcenterID = $(this).attr('data-id');
            if ($(this).hasClass('td-active')) {
                $('#leaderboardsquare').removeClass('tr-active');
                $(this).toggleClass('td-active');
                $(".subcenterdept_row").removeClass('hide');
                $(".subcenteruser_row").removeClass("hide");
                $("#selectedSubcenterId").val(0);
            } else {
                $('.subcenter_row').removeClass('td-active');
                $('#leaderboardsquare').addClass('tr-active');
                $(this).addClass('td-active');
                
                $(".subcenterdept_row").addClass('hide');        
                
                /*
                *Added for showing only selected sub center in sub center department
                */
                var deptscid = '';
                var activescid = '';
                activescid = $(".td-active").attr("data-id");
                $('#subcenterDepartment tr').each(function () {
                    deptscid = $(this).attr('data-subcenter');
                    if(activescid == deptscid) {
                        $(this).removeClass('hide');
                    } 
                });
                //ends here

                $(".subcenterdept_row_" + subcenterID).removeClass('hide');
                $(".subcenteruser_row").addClass("hide");
                $(".subcenteruser_" + subcenterID).removeClass("hide");
                $("#selectedSubcenterId").val(subcenterID);
            }
        });


        $(".subcenterdept_row").on("click", function () {
            var subcenterDeptID = $(this).attr('data-id');
            var subcenterID = $(this).attr('data-subcenter');

            if ($(this).hasClass('td-active')) {
                $('#subcenterDepartment').removeClass('tr-active');
                $(this).toggleClass('td-active');
                $(".subcenteruser_row").removeClass("hide");
                $(".subcenteruser_row").addClass("hide");
                $(".subcenteruser_" + subcenterID).removeClass("hide");
                $("#selectedDepartmentId").val(0);
            } else {
                $('.subcenterdept_row').removeClass('td-active');
                $('#subcenterDepartment').addClass('tr-active');
                $(this).addClass('td-active');
                $(".subcenteruser_row").addClass("hide");
                $(".subcenteruser_" + subcenterID + "_" + subcenterDeptID).removeClass("hide");
                $("#selectedDepartmentId").val(subcenterDeptID);
            }

        });


        $(".subcenteruser_row").on("click", function () {
            var subcenterUserID = $(this).attr('data-id');
            if ($(this).hasClass('td-active')) {
                $('#subcenterUser').removeClass('tr-active');
                $(this).toggleClass('td-active');
            } else {
                $('.subcenteruser_row').removeClass('td-active');
                $('#subcenterUser').addClass('tr-active');
                $(this).addClass('td-active');
            }
        });


        $('#subcenterUser').on('scroll', function () {
            let div = $(this).get(0);
            var totalrecords = $("#subcenterUser .subcenteruser_row").length;
            var organizationID = '<?php echo $organization_id; ?>';
            var startdaterandc = $("#startdaterandc_1").val();
            var enddaterandc = $("#enddaterandc_1").val();

            if (totalrecords < 100) {

            } else {
                if (div.scrollTop + div.clientHeight >= div.scrollHeight) {
                    console.log('Load new content');
                    $(".hiddenloader").removeClass("hidden");
                    setTimeout(function () {
                        $.ajax({
                            type: "POST",
                            dataType: 'html',
//                            url: siteurl + 'ajax/loadmoreleaderboardusers',
                            url: siteurl + 'reports/ndorsement_history_leaderboard_loadmore',
                            data: {totalrecords: totalrecords, organization_id: organizationID, startdate: startdaterandc, enddate: enddaterandc},
                            success: function (data, xhr) {
//                                console.log(data);
//                                return false;
                                req_sent = false;
                                if (data == "") {
                                    $(".hiddenloader").remove();
                                    return false;
                                }
                                $("#subCenterUserReport tbody").append(data);
                                $(".hiddenloader").addClass("hidden");

                            }
                        });
                    }, 1000)
                }
            }

        });

    });

</script>
<?php
$data = array(
    "textcenter" => "Announcements",
    "righttabs" => "1"
);
echo $this->Element('headerorg', array('data' => $data));
?>
<div class="settings"> <?php echo $this->Session->flash('auth'); ?>
    <p><?php echo $this->Session->Flash(); ?></p>
    <div class="col-md-12">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#announcements">Announcements</a></li>
        </ul>
        <div class="tab-content">
            <div id="announcements" class="tab-pane fade in active">
                <div class="form-group col-md-12 tnc mt10"> 
                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#org">Organizations</a></li>
                        <li><a data-toggle="tab" href="#users">Users</a></li>
                        <li><a data-toggle="tab" href="#dept">Departments</a></li>
                        <li><a data-toggle="tab" href="#suborg">Sub Organizations</a></li>
                    </ul>

                    <?php
                    if (!empty($userorgdata)) {
                        echo $this->Form->create("MailingOrg", array("enctype" => "multipart/form-data"));
                        //pr($orgdata);
                        $activities = array();
                        foreach ($userorgdata as $dataorg) {
                            if ($dataorg["Organization"]["name"] == "") {
                                continue;
                            }
                            $activities[] = array("name" => trim($dataorg["Organization"]["name"]), "value" => $dataorg["Organization"]["id"]);
                        }


                        $userList = array();
                        foreach ($userdata as $datauser) {
                            //$userList[] = array("name" => trim(base64_decode($datauser["User"]["fname"])) . " " . trim(base64_decode($datauser["User"]["lname"])), "value" => $datauser["User"]["id"]);
                            $userList[] = array("name" => trim($datauser["User"]["fname"]) . " " . trim($datauser["User"]["lname"]), "value" => $datauser["User"]["id"]);
                        }
                        $count = 1;
                        $userhtml = "";
                        for ($i = 0; $i < count($userList); $i++) {
                            $userhtml .= '<div searchuserannouncement = "' . $userList[$i]["name"] . '" class="col-md-3 checkbox"><input type="checkbox" name="data[User][Users][]" value="' . $userList[$i]["value"] . '" id="' . $userList[$i]["value"] . '" class="mailingcbclassuser announcementcheckbox css-checkbox"><label class="css-label" for="' . $userList[$i]["value"] . '"><span class="ellipsis iffyTip" >' . $userList[$i]["name"] . '</span></label></div>';
                            $count++;
                        }

                        $deptList = array();
//                    pr($deptdata); exit;
                        foreach ($deptdata as $datadept) {

                            $deptList[] = array("name" => trim($datadept["OrgDepartment"]["name"]) . " (" . trim($datadept["Organization"]["name"]) . ")", "value" => $datadept["OrgDepartment"]["id"]);
                        }
                        //pr($deptList); exit;
                        $count = 1;
                        $depthtml = "";
                        for ($i = 0; $i < count($deptList); $i++) {
                            $depthtml .= '<div searchdeptannouncement = "' . $deptList[$i]["name"] . '" class="col-md-3 checkbox"><input type="checkbox" name="data[User][Deprtment][]" value="' . $deptList[$i]["value"] . '" id="dept_' . $deptList[$i]["value"] . '" class="mailingcbclassdept announcementcheckbox css-checkbox"><label class="css-label" for="dept_' . $deptList[$i]["value"] . '"><span class="ellipsis iffyTip" >' . $deptList[$i]["name"] . '</span></label></div>';
                            $count++;
                        }


                        $subdeptList = array();
//                    pr($entitydata); exit;
                        foreach ($entitydata as $datasubdept) {
                            $subdeptList[] = array("name" => trim($datasubdept["Entity"]["name"]) . " (" . trim($datasubdept["Organization"]["name"]) . ")", "value" => $datasubdept["Entity"]["id"]);
                        }
//                    pr($subdeptList); exit;
                        $count = 1;
                        $subdepthtml = "";
                        for ($i = 0; $i < count($subdeptList); $i++) {
                            $subdepthtml .= '<div searchsuborgannouncement = "' . $subdeptList[$i]["name"] . '" class="col-md-3 checkbox"><input type="checkbox" name="data[User][SubOrg][]" value="' . $subdeptList[$i]["value"] . '" id="suborg_' . $subdeptList[$i]["value"] . '" class="mailingcbclasssuborg announcementcheckbox css-checkbox"><label class="css-label" for="suborg_' . $subdeptList[$i]["value"] . '"><span class="ellipsis iffyTip" >' . $subdeptList[$i]["name"] . '</span></label></div>';
                            $count++;
                        }
                        ?>
                        <!--<div class="form-group col-md-12 tnc">-->
                        <div class="tab-content">
                            <!-- ORGANIZATION SECTION -->
                            <div id="org" class="tab-pane fade in active">
                                <div class="col-md-12">
                                    <div class="search-icn" style="margin-top:10px;">
                                        <input type="text" name="searchannouncements" id="searchannouncements" placeholder="Search Organization..." class="form-control">
                                    </div>
                                    <div class="pull-left">
                                        <h5 class="notif">Select Organization from the List:</h5>
                                    </div>
                                    <div class="pull-right select-all">
                                        <?php
                                        if (!empty($activities)) {
                                            echo "<div class='checkbox'><input class='css-checkbox' id='mailingselectall' type='checkbox' name='selectallmailingorg' id='selectallmailingorg'><label class='css-label pull-right' for='mailingselectall' style='color:#fff;'>Select All</label></div>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="mail-to-org-checkbox">
                                    <div class="col-md-12" style="margin-top:20px;">
                                        <?php
                                        $count = 1;
                                        $html = "";
                                        for ($i = 0; $i < count($activities); $i++) {
                                            $html .= '<div searchorgannouncement = "' . $activities[$i]["name"] . '" class="col-md-3 checkbox"><input type="checkbox" name="data[User][Organizations][]" value="' . $activities[$i]["value"] . '" id="' . $activities[$i]["value"] . '" class="mailingcbclass announcementcheckbox css-checkbox"><label class="css-label" for="' . $activities[$i]["value"] . '"><span class="ellipsis iffyTip" >' . $activities[$i]["name"] . '</span></label></div>';
                                            $count++;
                                        }
                                        echo $html;
                                        ?>

                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <!-- USER SECTION -->
                            <div id="users" class="tab-pane fade">
                                <div class="col-md-12">
                                    <div class="search-icn" style="margin-top:10px;">
                                        <input type="text" name="searchannouncementsusers" id="searchannouncementsusers" placeholder="Search Users..." class="form-control">
                                    </div>
                                    <div class="pull-left">
                                        <h5 class="notif">Select users from the list:</h5>
                                    </div>
                                    <div class="pull-right select-all">
                                        <?php
                                        if (!empty($userList)) {
                                            echo "<div class='checkbox'><input class='css-checkbox' id='mailingselectalluser' type='checkbox' name='selectallmailingorguser'><label class='css-label pull-right' for='mailingselectalluser' style='color:#fff;'>Select All</label></div>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="mail-to-user-checkbox">
                                    <div class="col-md-12" style="margin-top:20px;">
                                        <?php echo $userhtml; ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <!-- DEPARTMENT SECTION -->
                            <div id="dept" class="tab-pane fade">
                                <div class="col-md-12">
                                    <div class="search-icn" style="margin-top:10px;">
                                        <input type="text" name="searchannouncementsdept" id="searchannouncementsdept" placeholder="Search Department..." class="form-control">
                                    </div>
                                    <div class="pull-left">
                                        <h5 class="notif">Select department from the list:</h5>
                                    </div>
                                    <div class="pull-right select-all">
                                        <?php
                                        if (!empty($depthtml)) {
                                            echo "<div class='checkbox'><input class='css-checkbox' id='mailingselectalldept' type='checkbox' name='mailingselectalldept'><label class='css-label pull-right' for='mailingselectalldept' style='color:#fff;'>Select All</label></div>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="mail-to-dept-checkbox">
                                    <div class="col-md-12" style="margin-top:20px;">
                                        <?php
                                        echo $depthtml;
                                        ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <!-- SUB-ORG SECTION -->
                            <div id="suborg" class="tab-pane fade">
                                <div class="col-md-12">
                                    <div class="search-icn" style="margin-top:10px;">
                                        <input type="text" name="searchannouncementssuborg" id="searchannouncementssuborg" placeholder="Search Sub Organizations..." class="form-control">
                                    </div>
                                    <div class="pull-left">
                                        <h5 class="notif">Select Sub Organization from the list:</h5>
                                    </div>
                                    <div class="pull-right select-all">
                                        <?php
                                        if (!empty($subdepthtml)) {
                                            echo "<div class='checkbox'><input class='css-checkbox' id='mailingselectallsuborg' type='checkbox' name='mailingselectallsuborg'><label class='css-label pull-right' for='mailingselectallsuborg' style='color:#fff;'>Select All</label></div>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div class="mail-to-suborg-checkbox">
                                    <div class="col-md-12" style="margin-top:20px;">
                                        <?php
                                        echo $subdepthtml;
                                        ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <!--  Department search end-->
                        </div>


                        <div class="clearfix"></div>
                        <!-- Date time added -->
                        <script src="http://momentjs.com/downloads/moment.js"></script>
                        <script src="http://momentjs.com/downloads/moment-timezone-with-data.js"></script>
                        <?php echo $this->Form->input('usertimzone', array('value' => '', 'type' => 'hidden', 'id' => 'usertimzone', 'name' => 'usertimzone')); ?>
                        <div class='col-sm-12 MT30'>
                            <div class="col-md-6 Posting">
                                <span class="radio">
                                    <div class="input radio">
                                        <div class="col-md-6">
                                            <input type="radio" name="report_type" id="postnow" class="postclick"  value="postnow">
                                            <label for="postnow">Announce Now</label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="radio" name="report_type" class="postclick" id="postlater" value="postlater" checked="checked">
                                            <label for="postlater">Announce Later</label>
                                        </div>
                                    </div>
                                </span>
                            </div>
                            <div class="date-pickers col-md-6">
                                <span class="pull-left col-md-3 notif" style="top: 5px; color: #fff; text-align: right; right: 0;" >Date & Time</span>
                                <div class='col-md-4'>
                                    <?php
//pr($announcementData[0]); exit;
                                    $formateToPutInInput = date("m/d/Y", time());
                                    ?>
                                    <div class="form-group">
                                        <div class='input-group date' id='datetimepicker3'>
                                            <input type='text' name="post_date" id="post_date" class="form-control" value="<?php echo $formateToPutInInput; ?>" />
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class='col-md-4'>
                                    <div class="form-group">
                                        <div class='input-group date' id='datetimepicker4'>
                                            <input type='text' name="post_time" id="post_time" class="form-control"/>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <span class="error col-md-offset-2 datetimeerror" style="display: none;">*Please select date and time to post</span>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <!-- Date time end -->

                        <div class="clearfix"></div>
                        <h5 class="notif">Message:</h5>
                        <?php
                        echo $this->Tinymce->input('User.mailingbox', array(
                            'label' => false,
                                ), array(
                            'language' => 'en'
                                ), 'full'
                        );
                        echo $this->Form->input('messagebox',array('type' => 'text','id'=>'messagebox','label'=>false, 'class' => 'hidden',));
                        ?>
                        <div class="clearfix"></div>
                        <br>
                        <div>
                            <?php //echo $this->Form->input('attachment',array('type' => 'file','label'=>false, 'class' => 'hidden', 'accept'=>"image/*,.ppt,.pptx,.doc,.docx,.xls,.xlsx,.pdf"));  ?>
                            <!--                        <button class="btn btn-default" id="addattachemnt_announcement" type="button">Add Attachment</button>
                                                    <br />
                                                    <br />
                                                    <span class="fileSupported">Supported files - .ppt/.pptx/.doc/.docx/.xls/.xls/.pdf/all image files</span>
                                                    <br/>
                                                    <span class="fileSupported">Maximum file size - 10 mb</span>
                                                    <br><br>-->
                            <input type="hidden" name="formname" value="mailingorganizationsorgadmin"/>
                            <button id="mailingorgadmin_submit_setting" class="btn btn-blue save" type="button">Send</button>
                        </div>
                    </div>
                    <?php
                    echo $this->Form->end();
                } else {
                    ?>
                    <h3 class="nDorse-support text-center">You are not authorized to access this feature. Please contact 
                        <a href="mailto:support@ndorse.net" target="_top">nDorse support.</a>
                    </h3>
                <?php }
                ?>
                <!--</div>-->
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <script>
        $(function () {
            var dateNow = new Date();
            $('#datetimepicker4').datetimepicker({
//            format: 'LT',
                format: 'HH:mm',
                defaultDate: dateNow
//            use24hours: true
//            minDate:moment()
            });
            $('#datetimepicker3').datetimepicker({
                // viewMode: 'months',
                format: 'MM/DD/YYYY',
                // minDate: dateNow,
                defaultDate: dateNow
//             minDate:moment()
            });

            $('.postclick').on("click", function () {
                var postType = $(this).val();
                if (postType == 'postnow') {
                    $(".date-pickers").fadeOut('slow');
                } else {//postlater
                    $(".date-pickers").fadeIn('slow');
                }
                console.log(postType);
            });
        });

        var usertimzone = moment.tz.guess();
        $("#usertimzone").val(usertimzone);



    </script>
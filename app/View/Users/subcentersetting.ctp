<?php
$data = array(
    "textcenter" => "Sub Center Settings",
    "righttabs" => "1"
);

echo $this->Element('header', array('data' => $data));

foreach ($subCenterData as $index => $subCenters) {
    $subcenterDropDown[$subCenters['OrgSubcenter']['id']] = $subCenters['OrgSubcenter']['short_name'];
}
//pr($subCenterData);exit;
?>
<!--Conatiner start here-->
<div class="settings"> <?php echo $this->Session->flash('auth'); ?>
    <p id="flashmessage"><?php echo $this->Session->Flash(); ?></p>
    <?php
    echo $this->Form->create('Organization');
    ?>
    <div class="col-md-12">
        <ul class="nav nav-tabs">
            <?php
            $ActiveClass = 'active';
            $subCenterCount = count($subCenterData);
            if (isset($subCenterData) && count($subCenterData) > 0) {
                foreach ($subCenterData as $index => $sCenter) {
                    $sCenterShortName = $sCenter['OrgSubcenter']['short_name'];
                    $sCenterID = $sCenter['OrgSubcenter']['id'];
                    ?>
                    <li class="<?php echo $ActiveClass; ?>"><a data-toggle="tab" href="#<?php echo $sCenterID; ?>"><?php echo $sCenterShortName; ?></a></li>
                    <?php
                    $ActiveClass = '';
                }
                ?>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <?php
            if (count($subCenterData) > 0) {
                $ActiveClass = 'active';
                foreach ($subCenterData as $index => $sCenter) {
                    $sCenterID = $sCenter['OrgSubcenter']['id'];
                    ?>
                    <div id="<?php echo $sCenterID; ?>" class="tab-pane fade in <?php echo $ActiveClass; ?>">
                        <section class="AddCoreValue">
                            <div class="AddCoreValue">
                                <div class="col-md-12">
                                    <div class="pull-left">
                                        <h4 class="require">SET CORE VALUES / SET GOALS</h4>
                                    </div>

                                </div>
                            </div>
                            <?php
                            $counter_core_values = (isset($existing_corevalues)) ? count($existing_corevalues) : "";
                            $display = "display:none";
                            if ($counter_core_values) {
                                $display = "display:block";
                            }
                            ?>
                            <div class="CreateEditOrg" id="corevaluetable" style="<?php echo $display; ?>" >
                                <table id="addcoretable" class="table table-hover cvTable" >
                                    <tr id="addcorerow_0">
                                        <th >For DAISY</th>
                                        <th >For Webapp</th>
                                        <th >For Feedback</th>
                                        <th >Active</th>
                                        <th >Core value</th>
                                    </tr>
                                    <?php
                                    if (isset($existing_corevalues)) {
//                                        pr($subCenterCoreValuesArray); exit;
                                        ?>
                                        <?php
                                        for ($i = 0; $i < $counter_core_values; $i++) {
                                            $others_department = "";
                                            $coreValueID = $existing_corevalues[$i]['org_core_values']['id'];

                                            $subCoreValueID = $checkedDaisy = $checkedWebapp = $checkedGuest = $checked = '';
                                            if (isset($subCenterCoreValuesArray[$sCenterID][$coreValueID])) {
                                                $checkedDaisy = $subCenterCoreValuesArray[$sCenterID][$coreValueID]['for_daisy'] == 1 ? "checked" : "";
                                                $checkedWebapp = $subCenterCoreValuesArray[$sCenterID][$coreValueID]['for_web'] == 1 ? "checked" : "";
                                                $checkedGuest = $subCenterCoreValuesArray[$sCenterID][$coreValueID]['for_feedback'] == 1 ? "checked" : "";
                                                $checked = $subCenterCoreValuesArray[$sCenterID][$coreValueID]['status'] == 1 ? "checked" : "";
                                                $subCoreValueID = $subCenterCoreValuesArray[$sCenterID][$coreValueID]['id'];
                                            }
                                            ?>

                                            <input type="hidden" id="subc_core_id" name="data[<?php echo $sCenterID; ?>][subc_core_id][<?php echo $i; ?>]" value="<?php echo $subCoreValueID; ?>"> 
                                            <tr id="addcorerow_<?php echo $i + 1; ?>">
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox" id="cvfordaisy_<?php echo $i + 1; ?>_<?php echo $sCenterID; ?>" class="css-checkbox" <?php echo $checkedDaisy; ?> value="1" name="data[<?php echo $sCenterID; ?>][cvfordaisy][<?php echo $i; ?>]">
                                                        <label for="cvfordaisy_<?php echo $i + 1; ?>_<?php echo $sCenterID; ?>" class="css-label"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox" id="cvforweb_<?php echo $i + 1; ?>_<?php echo $sCenterID; ?>" class="css-checkbox" <?php echo $checkedWebapp; ?> value="1" name="data[<?php echo $sCenterID; ?>][cvforweb][<?php echo $i; ?>]">
                                                        <label for="cvforweb_<?php echo $i + 1; ?>_<?php echo $sCenterID; ?>" class="css-label"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox" id="cvforguest_<?php echo $i + 1; ?>_<?php echo $sCenterID; ?>" class="css-checkbox" <?php echo $checkedGuest; ?> value="1" name="data[<?php echo $sCenterID; ?>][cvforguest][<?php echo $i; ?>]">
                                                        <label for="cvforguest_<?php echo $i + 1; ?>_<?php echo $sCenterID; ?>" class="css-label"></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="checkbox">
                                                        <input type="checkbox" id="cvid_<?php echo $i + 1; ?>_<?php echo $sCenterID; ?>" class="OrgCvactivestatus css-checkbox" value="1" <?php echo $checked; ?> name="data[<?php echo $sCenterID; ?>][cvactivestatus][<?php echo $i; ?>]">
                                                        <label for="cvid_<?php echo $i + 1; ?>_<?php echo $sCenterID; ?>" class="css-label"></label>
                                                    </div>
                                                    <input type="hidden" id="hiddenid" name="data[<?php echo $sCenterID; ?>][cv_hiddenid][]" value="<?php echo $existing_corevalues[$i]['org_core_values']['id']; ?>">
                                                </td>
                                                <td id="corevaluesdropdown">
                                                    <p id='corevalue'><?php echo $existing_corevalues[$i]['org_core_values']['name']; ?></p>
                                                </td>

                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </table>
                            </div>
                        </section>

                        <section class="AddCoreValue">
                            <div class="AddCoreValue">
                                <div class="col-md-12">
                                    <div class="pull-left">
                                        <h4 class="require"> SET HASHTAGS</h4>
                                    </div>

                                </div>
                            </div>
                            <?php
                            $counter_htags = (isset($existing_hashtags)) ? count($existing_hashtags) : "";
                            $display = "display:none";
                            if ($counter_htags || !empty($errormsg)) {
                                $display = "display:block";
                                if (!empty($errormsg)) {
                                    if (empty($resultantdepartment["entity"]["value"])) {
                                        $display = "display:none";
                                    }
                                }
                            }
                            ?>
                            <div class="CreateEditOrg" id="addentitydiv" style="<?php echo $display; ?>">
                                <table class="table table-hover" id="addentitytable">
                                    <tr>
                                        <th width="15%">Active</th>
                                        <th width="45%">Hashtag</th>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    if (isset($existing_hashtags)) {

                                        foreach ($existing_hashtags as $id => $value) {
                                            $sub_hashtag_id = '';
                                            $sub_hashtag_status = 0;
//                                            pr($subCenterHashtagArray[$sCenterID][$id]);
                                            if (isset($subCenterHashtagArray[$sCenterID][$id])) {
                                                $sub_hashtag_id = $subCenterHashtagArray[$sCenterID][$id]['id'];
                                                $sub_hashtag_status = $subCenterHashtagArray[$sCenterID][$id]['status'];
                                            }
                                            ?>
                                            <tr id="addentity_<?php echo $i; ?>">
                                                <td width="15%">
                                                    <input type="hidden" id="sc_hashtag_id" name="data[<?php echo $sCenterID; ?>][sc_hashtag_id][<?php echo $i; ?>]" value="<?php echo $sub_hashtag_id; ?>"> 
                                                    <div class="checkbox">
                                                        <input type="checkbox" id="hashtag_<?php echo $i; ?>_<?php echo $sCenterID; ?>" value="1" name="data[<?php echo $sCenterID; ?>][hashtag][<?php echo $i; ?>]" <?php echo ($sub_hashtag_status == 1) ? "checked" : " "; ?> class="entitycheckbox css-checkbox" />
                                                        <label for="hashtag_<?php echo $i; ?>_<?php echo $sCenterID; ?>" class="css-label"></label>
                                                    </div>
                                                    <input type="hidden" class="hashtagid" name="data[<?php echo $sCenterID; ?>][hashtaghiddenid][<?php echo $i; ?>]" value="<?php echo $id; ?>">
                                                </td>
                                                <td width="45%" class="entityvaluestextbox"><p class = corevalue><?php echo $value; ?></p>
                                                    <input class="entitytextbox" type="hidden" value="<?php echo $value; ?>" class="entitytextbox" />
                                                </td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                </table>
                            </div>
                        </section>

                        <section class="AddCoreValue">
                            <div class="AddCoreValue">
                                <div class="col-md-12">
                                    <div class="pull-left">
                                        <h4 class="require"> SET SUB ORGANIZATION</h4>
                                    </div>

                                </div>
                            </div>
                            <?php
                            $counter_entities = (isset($existing_entities)) ? count($existing_entities) : "";
                            $display = "display:none";
                            if ($counter_entities || !empty($errormsg)) {
                                $display = "display:block";
                                if (!empty($errormsg)) {
                                    if (empty($resultantdepartment["entity"]["value"])) {
                                        $display = "display:none";
                                    }
                                }
                            }
                            ?>
                            <div class="CreateEditOrg" id="addentitydiv" style="<?php echo $display; ?>">
                                <table class="table table-hover" id="addentitytable">
                                    <tr>
                                        <th width="15%">Active</th>
                                        <th width="45%">Sub Organization</th>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    if (isset($existing_entities)) {
                                        foreach ($existing_entities as $id => $value) {
                                            //pr($subCenterEntityArray[$sCenterID][$id]);
                                            $sub_entity_id = '';
                                            $sub_entity_status = 0;
                                            if (isset($subCenterEntityArray[$sCenterID][$id])) {
                                                $sub_entity_id = $subCenterEntityArray[$sCenterID][$id]['id'];
                                                $sub_entity_status = $subCenterEntityArray[$sCenterID][$id]['status'];
                                            }
                                            $others_department = "";
                                            ?>
                                            <tr id="addentity_<?php echo $i; ?>">
                                                <td width="15%">
                                                    <input type="hidden" id="sc_entity_id" name="data[<?php echo $sCenterID; ?>][sc_entity_id][<?php echo $i; ?>]" value="<?php echo $sub_entity_id; ?>"> 
                                                    <div class="checkbox">
                                                        <input type="checkbox" id="entityid_<?php echo $i; ?>_<?php echo $sCenterID; ?>" value="1" name="data[<?php echo $sCenterID; ?>][entity][<?php echo $i; ?>]" <?php echo ($sub_entity_status == 1) ? "checked" : " "; ?> class="entitycheckbox css-checkbox" />
                                                        <label for="entityid_<?php echo $i; ?>_<?php echo $sCenterID; ?>" class="css-label"></label>
                                                    </div>
                                                    <input type="hidden" class="entityid" name="data[<?php echo $sCenterID; ?>][entityhiddenid][<?php echo $i; ?>]" value="<?php echo $id; ?>">
                                                </td>
                                                <td width="45%" class="entityvaluestextbox"><p class = corevalue><?php echo $value; ?></p>
                                                    <input class="entitytextbox" type="hidden" value="<?php echo $value; ?>" class="entitytextbox" />
                                                </td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                </table>
                            </div>
                        </section>
                        <!--departments-->
                        <section class="AddCoreValue">
                            <div class="AddCoreValue">
                                <div class="col-md-12">
                                    <div class="pull-left">
                                        <h4>SET DEPARTMENTS</h4>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $counter_departments = (isset($existing_departments)) ? count($existing_departments) : "";
                            $display = "display:none";
                            $counter_departments;
                            if ($counter_departments || !empty($errormsg)) {
                                $display = "display:block";
                                if (!empty($errormsg)) {
                                    if (empty($resultantdepartment["department"]["value"])) {
                                        $display = "display:none";
                                    }
                                }
                            }
                            ?>
                            <div class="CreateEditOrg" id="adddepartmentdiv" style="<?php echo $display; ?>">
                                <table class="table table-hover" id="adddepartmenttable">
                                    <tr>
                                        <th width="15%">Active</th>
                                        <th width="45%">Department Name</th>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    if (isset($existing_departments)) {
                                        foreach ($existing_departments as $id => $value) {

                                            $sc_dept_id = '';
                                            $sub_dept_status = 0;
                                            //pr($subCenterDeptArray[$sCenterID][$id]);
                                            if (isset($subCenterDeptArray[$sCenterID][$id])) {
                                                $sc_dept_id = $subCenterDeptArray[$sCenterID][$id]['id'];
                                                $sub_dept_status = $subCenterDeptArray[$sCenterID][$id]['status'];
                                            }
                                            ?>
                                            <tr id="adddepartment_<?php echo $i ?>">
                                                <td width="15%">
                                                    <input type="hidden" id="sc_dept_id" name="data[<?php echo $sCenterID; ?>][sc_dept_id][<?php echo $i; ?>]" value="<?php echo $sc_dept_id; ?>"> 
                                                    <div class="checkbox">
                                                        <input type="checkbox" id="departmentid_<?php echo $i; ?>_<?php echo $sCenterID; ?>" value="1" name="data[<?php echo $sCenterID; ?>][department][<?php echo $i; ?>]" <?php echo ($sub_dept_status == 1) ? "checked" : ""; ?> class="departmentcheckbox css-checkbox" />
                                                        <label for="departmentid_<?php echo $i; ?>_<?php echo $sCenterID; ?>" class="css-label"></label>
                                                    </div>
                                                    <input type="hidden" class="departmentdbid" name="data[<?php echo $sCenterID; ?>][departmenthiddenid][<?php echo $i; ?>]" value="<?php echo $id; ?>">
                                                <td width="45%" class="departmentselectrow">
                                                    <p class='departmentv'><?php echo $value; ?></p>
                                                </td>   
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                    }
                                    ?>
                                </table>
                            </div>
                        </section>
                        <!--<section class="AddCoreValue">
                            <div class="AddCoreValue">
                                <div class="col-md-12">
                                    <div class="pull-left">
                                        <h4>ENTER JOB TITLES</h4>
                                    </div>

                                </div>
                            </div>
                            <?php /*
                            $counter_jobtitles = (isset($existing_jobtitles)) ? count($existing_jobtitles) : "";
                            $display = "display:none";
                            if ($counter_jobtitles || !empty($errormsg)) {
                                $display = "display:block";
                                if (!empty($errormsg)) {
                                    if (empty($resultantdepartment["jobtitle"]["value"])) {
                                        $display = "display:none";
                                    }
                                }
                            }
                            ?>
                            <div class="CreateEditOrg" id="addjobtitlediv" style="<?php echo $display; ?>">
                                <table class="table table-hover" id="addjobtitletable">
                                    <tr>
                                        <th width="15%">Active</th>
                                        <th width="45%">Job Title</th>
                                    </tr>
                                    <?php
                                    $i = 1;
                                    if (isset($existing_jobtitles)) {
                                        foreach ($existing_jobtitles as $id => $value) {
                                            $sc_jobtitle_id = "";
                                            $sub_jobtitle_status = 0;
                                            if (isset($subCenterJobtitleArray[$sCenterID][$id])) {
                                                $sc_jobtitle_id = $subCenterJobtitleArray[$sCenterID][$id]['id'];
                                                $sub_jobtitle_status = $subCenterJobtitleArray[$sCenterID][$id]['status'];
                                            }
                                            ?>
                                            <tr id="addjobtitle_<?php echo $i ?>">
                                                <td width="15%">
                                                    <input type="hidden" id="sc_jobtitle_id" name="data[<?php echo $sCenterID; ?>][sc_jobtitle_id][<?php echo $i; ?>]" value="<?php echo $sc_jobtitle_id; ?>"> 
                                                    <div class="checkbox">
                                                        <input type="checkbox" id="jbactive_<?php echo $i; ?>_<?php echo $sCenterID; ?>" value="1" name="data[<?php echo $sCenterID; ?>][jobtitle][<?php echo $i ?>]" <?php echo ($sub_jobtitle_status == 1) ? "checked" : ""; ?> class="jobtitlecheckbox css-checkbox" />
                                                        <label for="jbactive_<?php echo $i; ?>_<?php echo $sCenterID; ?>" class="css-label"></label>
                                                    </div>
                                                    <input type="hidden" class="jobtitlebid" name="data[<?php echo $sCenterID; ?>][jobtitlehiddenid][<?php echo $i ?>]" value="<?php echo $id; ?>">
                                                <td width="45%" class="jobtitleselectrow">
                                                    <p class='jobtitlev'><?php echo $value; ?></p>
                                                </td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                    } */
                                    ?>
                                </table>
                            </div>
                        </section> -->
                    </div>
                    <?php
                    $ActiveClass = '';
                }
            }
            ?>
            <div class="clearfix"></div>
        </div>    
    </div>
    <script>
        $(document).ready(function () {
            $("#reset_setting").click(function () {
                location.href = '<?php echo $prev_page; ?>';
            });
        });

        $(document).on('mouseenter', ".iffyTip", function () {
            var $this = $(this);
            if (this.offsetWidth < this.scrollWidth && !$this.attr('title')) {
                $this.tooltip({
                    title: $this.text(),
                    placement: "bottom"
                });
                $this.tooltip('show');
            }
        });
    </script> 
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
</div>
<section class="container-fluid footer-bg">
    <div class="container">
        <div class="row">
            <div class="pull-right">
                <button type="button" class="btn btn-default" id="orgformcancel">Cancel</button>
                <button type="button" class="btn btn-default" id="subcenterformsubmit">Save</button>
            </div>
        </div>
    </div>
</section>
<?php echo $this->Form->end(); ?>
<script>
    $("#subcenterformsubmit").click(function () {
        var errorFlag = false;
        $(".err").hide();
        if (!errorFlag) {
            
            $("#OrganizationSubcentersettingForm").submit();
            
            
            
            
        }
    });
</script>
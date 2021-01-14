<?php
echo $this->Html->script("bootstrap-colorpicker.min.js");
echo $this->Html->script("bootstrap-colorpicker-plus.js");
echo $this->Html->css("bootstrap-colorpicker.min.css");
echo $this->Html->css("bootstrap-colorpicker-plus.css");
//==================condition when wrong data is entered so needs to refill the dynamic data
$resultantdepartment = '';
if (!empty($errormsg)) {
    //=====================for departments entities and jobtitles
    $resultantdepartment["department"] = array("value" => array(), "activestatus" => array(), "savestatus" => array());
    $resultantdepartment["jobtitle"] = array("value" => array(), "activestatus" => array(), "savestatus" => array());
    $resultantdepartment["entity"] = array("value" => array(), "activestatus" => array(), "savestatus" => array());
    $resultantdepartment["corevalues"] = array("value" => array(), "colorcode" => array(), "activestatus" => array(), "savestatus" => array());
    $array = array("department", "jobtitle", "entity");
    foreach ($array as $orgmenus) {
        $valuearray = array();
        $activestatusarray = array();
        $savestatusarray = array();
        $counterdepartments = count($this->request->data['Org'][$orgmenus . 'active']);
        for ($i = 0; $i < $counterdepartments; $i++) {
            $change = ($orgmenus == "department") ? "departments" : $orgmenus;
            if ($orgmenus == "entity") {
                $value = $this->request->data['Org']["entitytextbox"][$i];
            } else {
                $value = $this->request->data['Org'][$change][$i];
            }
            $activestatus = $this->request->data['Org'][$orgmenus . 'active'][$i];
            $savestatus = $this->request->data['Org'][$orgmenus . 'save'][$i];
            if ($value == "other") {
                $value = $this->request->data['Org'][$orgmenus . '_other_department'][$i];
            }
            array_push($valuearray, $value);
            array_push($activestatusarray, $activestatus);
            array_push($savestatusarray, $savestatus);
        }
        $resultantdepartment[$orgmenus]["value"] = $valuearray;
        $resultantdepartment[$orgmenus]["activestatus"] = $activestatusarray;
        $resultantdepartment[$orgmenus]["savestatus"] = $savestatusarray;
    }
    //=====================for corevalues
    $countercorevalues = count($this->request->data['Org']['cvactive']);
    $valuearray = array();
    $activestatusarray = array();
    $savestatusarray = array();
    $savecolorcodearray = array();
    for ($i = 0; $i < $countercorevalues; $i++) {
        $value = $this->request->data['Org']["corevalues"][$i];
        $activestatus = $this->request->data['Org']['cvactive'][$i];
        $savestatus = $this->request->data['Org']['save'][$i];
        if ($value == "other") {
            $value = $this->request->data['Org']['other_department'][$i];
        }
        $colorcodes = $this->request->data['Org']['cp'][$i];
        array_push($valuearray, $value);
        array_push($activestatusarray, $activestatus);
        array_push($savestatusarray, $savestatus);
        array_push($savecolorcodearray, $colorcodes);
    }
    $resultantdepartment["corevalues"]["value"] = $valuearray;
    $resultantdepartment["corevalues"]["activestatus"] = $activestatusarray;
    $resultantdepartment["corevalues"]["savestatus"] = $savestatusarray;
    $resultantdepartment["corevalues"]["colorcode"] = $savecolorcodearray;
    //pr($resultantdepartment);
}
?>
<script>
    var js_arraycv = <?php echo json_encode($corevalues); ?>;
    var js_arraydept = <?php echo json_encode($departments); ?>;
    var js_arrayjt = <?php echo json_encode($jobtitles); ?>;
    $(document).ready(function () {
        //========================bind add core values
        $('#addcorevalues').bind("click", function () {
            if ($('#corevaluetable').is(":visible")) {
                var corevalues = '<?php echo json_encode($corevalues); ?>';
                var cvalues = JSON.parse(corevalues);
                //var rowlength = $("#addcoretable tr").length;
                var rowlength = parseInt($("#addcoretable tr:last").attr("id").split("_")[1]) + 1;

                //===============check if rowlength for id already Exist
//                var arrayrowlengtscheck = new Array();
//                $("#addcoretable tr").each(function () {
//                    if (typeof $(this).attr("id") != "undefined") {
//                        var presentids = $(this).attr("id");
//                        var splittingids = presentids.split("_");
//                        var presentid = splittingids[1];
//                        
//                        arrayrowlengtscheck.push(presentid)
//                    }
//                })
//                //=====mapping string to number
//                arrayrowlengtscheck = arrayrowlengtscheck.map(Number);
//                
//                //console.log($.inArray(rowlength, arrayrowlengtscheck));
//                if ($.inArray(rowlength, arrayrowlengtscheck) > -1) {
//                    rowlength = rowlength + "-1"
//                }
                //console.log(rowlength);
                $('<tr id="addcorerow_' + rowlength + '">').appendTo("#addcoretable");
                $('<td><div class="checkbox"><input type="checkbox" class="OrgCvactivestatus css-checkbox" id="cvid_' + rowlength + '" value="active" checked="checked" name="data[Org][cvactivestatus][]"><label for="cvid_' + rowlength + '" class="css-label"></label></div><input type="hidden" id="cvactive" name="data[Org][cvactive][]" value="active"><input class="cvsaveunsave" id="saveunsave" type="hidden" value="unsave" name="data[Org][save][]"></td>').appendTo("#addcorerow_" + rowlength);
                $('<td id="corevaluesdropdown"><div class="input select"><div class="select-style"><select id="OrgCorevalues" class="form-control" name="data[Org][corevalues][]"><option value="">Select Core Value</option></select></div></div><div style="display:none" id="othercv"><div class="input text"><input type="text" id="OrgOtherDepartment" class="form-control" name="data[Org][other_department][]"></div></div></td>').appendTo("#addcorerow_" + rowlength);
                $('<td><input type="hidden" name="data[Org][cp][]" value="#FFFFFF"><input style="color:#FFFFFF; background-color:#001e52" type="text" value="Hello" id="colorpick"/></td>').appendTo("#addcorerow_" + rowlength);
                $('<td id="savecv"><?php echo $this->Html->image("EditRow.png", array("id" => "editcorevalues")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savecorevalues")); ?></td>').appendTo("#addcorerow_" + rowlength);
                $('<td id="deletecv"><?php echo $this->Html->image("DeleteRow.png", array("id" => "deletecorevalues")); ?></td>').appendTo("#addcorerow_" + rowlength);
                $('</tr>').appendTo("#addcorerow_" + rowlength);
                for (tmpvalues in cvalues) {
                    $('<option value="' + cvalues[tmpvalues] + '">' + cvalues[tmpvalues] + '</option>').appendTo("#addcorerow_" + rowlength + " #OrgCorevalues");
                }
                //==================to add color picker to next row
                var demo1 = $('#addcorerow_' + rowlength + ' #colorpick');
                demo1.colorpickerplus();
                demo1.on('changeColor', function (e, color) {
                    if (color == null)
                        $(this).val('transparent').css('color', '#FFFFFF');//tranparent
                    else
                        $(this).val("Hello").css('color', color);
                    $($(this).parent().find("input[type ='hidden']")[0]).val(color);
                    $(this).css('background-color', "#001e52");
                });
            }
            $("#corevaluetable").show();
        });

        //====================adding new entity dynamically
        $(document).on('click', '#addentity', function () {
            //var rowlength = $("#addentitytable tr").length;
            var rowlength = parseInt($("#addentitytable tr:last").attr("id").split("_")[1]) + 1;
            if (rowlength == 1 || $('#addentitydiv').is(":visible")) {
                $('<tr id="addentity_' + rowlength + '">').appendTo('#addentitytable');
                $('<td><div class="checkbox"><input type="checkbox" class="entitycheckbox css-checkbox" checked="" name="entirycheckbox" id="entityid_' + rowlength + '"> <label for="entityid_' + rowlength + '" class="css-label"></label></div><input type="hidden" class="entityactive" name="data[Org][entityactive][]" value="active"><input type="hidden" class="entitysaveunsave" name="data[Org][entitysave][]" value="unsave"></td>').appendTo('#addentity_' + rowlength + '');
                $('<td class="entityvaluestextbox"><input placeholder="Add Sub Organization" type="textbox" name="data[Org][entitytextbox][]" class="entitytextbox"></td>').appendTo('#addentity_' + rowlength + '');
                $('<td><?php echo $this->Html->image("EditRow.png", array("class" => "editentity")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "saveentity")); ?></td>').appendTo('#addentity_' + rowlength + '');
                $('<td><?php echo $this->Html->image("DeleteRow.png", array("class" => "deleteentity")); ?></td>').appendTo('#addentity_' + rowlength + '');
                $('</tr>').appendTo('#addentity_' + rowlength + '');
            }
            $("#addentitydiv").show();
        });

        //================adding new department dynamically
        $(document).on('click', '#adddepartment', function () {
            if ($('#adddepartmentdiv').is(":visible")) {
                var departmentvalues = '<?php echo json_encode($departments); ?>';
                var dvalues = JSON.parse(departmentvalues);
                //var rowlength = $("#adddepartmenttable tr").length;
                var rowlength = parseInt($("#adddepartmenttable tr:last").attr("id").split("_")[1]) + 1;
                $('<tr id="adddepartment_' + rowlength + '">').appendTo("#adddepartmenttable");
                $('<td><div class="checkbox"><input type="checkbox" class="entitycheckbox css-checkbox" id="departmentid_' + rowlength + '" checked="" name="departmentcheckbox"><label class="css-label" for="departmentid_' + rowlength + '"></label></div><input type="hidden" value="active" name="data[Org][departmentactive][]" class="departmentactive"><input type="hidden" value="unsave" name="data[Org][departmentsave][]" class="departmentsaveunsave"></td>').appendTo("#adddepartment_" + rowlength);
                $('<td class="departmentselectrow"><div class="select-style"><select name="data[Org][departments][]" class="departmentvalues form-control valid"><option value="">Select Department</option></select></div><input type="hidden" name="data[Org][department_other_department][]" class="form-control other_department"></td>').appendTo("#adddepartment_" + rowlength);
                $('<td><?php echo $this->Html->image("EditRow.png", array("class" => "editdepartment")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savedepartment")); ?></td>').appendTo("#adddepartment_" + rowlength);
                $('<td><?php echo $this->Html->image("DeleteRow.png", array("class" => "deletedepartment")); ?></td>').appendTo("#adddepartment_" + rowlength);
                $('</tr>').appendTo("#adddepartment_" + rowlength);
                for (tmpvalues in dvalues) {
                    $('<option value="' + dvalues[tmpvalues] + '">' + dvalues[tmpvalues] + '</option>').appendTo("#adddepartment_" + rowlength + " .departmentvalues");
                }
            }
            $("#adddepartmentdiv").show();
        });

        //===============adding new jobtitle dynamically
        $(document).on('click', '#addjobtitle', function () {
            if ($('#addjobtitlediv').is(":visible")) {
                var jobtitlevalues = '<?php echo json_encode($jobtitles); ?>';
                var jvalues = JSON.parse(jobtitlevalues);
                //var rowlength = $("#addjobtitletable tr").length;
                var rowlength = parseInt($("#addjobtitletable tr:last").attr("id").split("_")[1]) + 1;
                $('<tr id="addjobtitle_' + rowlength + '">').appendTo("#addjobtitletable");
                $('<td><div class="checkbox"><input id="jbactive_' + rowlength + '" type="checkbox" class="jobtitlecheckbox css-checkbox" checked="" name="jobtitlecheckbox"><label for="jbactive_' + rowlength + '" class="css-label"></label><input type="hidden" value="active" name="data[Org][jobtitleactive][]" class="jobtitleactive"><input type="hidden" value="unsave" name="data[Org][jobtitlesave][]" class="jobtitlesaveunsave"></td>').appendTo("#addjobtitle_" + rowlength);
                $('<td class="jobtitleselectrow"><div class="select-style"><select name="data[Org][jobtitle][]" class="jobtitlevalues form-control valid"><option value="">Select Job Title</option></select></div><input type="hidden" name="data[Org][jobtitle_other_department][]" class="form-control other_jobtitle"></td>').appendTo("#addjobtitle_" + rowlength);
                $('<td><?php echo $this->Html->image("EditRow.png", array("class" => "editjobtitle")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savejobtitle")); ?></td>').appendTo("#addjobtitle_" + rowlength);
                $('<td><?php echo $this->Html->image("DeleteRow.png", array("class" => "deletejobtitle")); ?></td>').appendTo("#addjobtitle_" + rowlength);
                $('</tr>').appendTo("#addjobtitle_" + rowlength);
                for (tmpvalues in jvalues) {
                    $('<option value="' + jvalues[tmpvalues] + '">' + jvalues[tmpvalues] + '</option>').appendTo("#addjobtitle_" + rowlength + " .jobtitlevalues");
                }
            }
            $("#addjobtitlediv").show();
        });
    });

</script>

<div class="col-md-12"> <?php echo $this->Form->create('Org', array("enctype" => "multipart/form-data")); ?>
    <section>

        <div class="row createEditOrg">
            <div class="col-lg-12 mt10 f-center">
                <?php
                echo $this->Html->image('comp_pic.png', array('width' => '214', 'id' => 'org_image'));
                ?>
                <div class="hidden-lg"></div>
                <button type="button"  id="org_upload_photo" class="btn btn-blue">Upload Picture</button>
                <button type="button" id="org_remove_photo" class="btn btn-blue">Remove Picture</button>
            </div>
        </div>
        <label id="validImageError" class="error"></label> 
        <?php
        echo $this->Form->input('Image', array(
            'type' => 'file',
            'id' => 'photo',
            'label' => false,
            'class' => 'btn_uplaod_file hidden',
            'accept' => ".jpg,.png,.gif,.jpeg",
        ));
        ?>
    </section>
    <?php if ($errormsg != '') { ?>
        <div class="error-createclient"><?php echo $errormsg; ?></div>
    <?php } ?>
    <!--ends here-->
    <?php //echo $this->Form->create('Org'); ?>
    <?php echo $this->Form->input('image', array('class' => 'form-control', 'label' => false, 'type' => 'hidden', 'id' => 'org_image_name')); ?>
    <section>
        <div class="row">
            <section>
                <div class="row">
                    <div class="col-md-6"> 
                        <!--<div class="col-md-1"></div> -->
                        <div class="col-md-3">
                            <div class="labelCus require">Name</div>
                        </div>
                        <div class="col-md-9"> <?php echo $this->Form->input('name', array('placeholder' => 'Name', 'class' => 'form-control', 'label' => false)); ?> </div>
                    </div>
                </div>
            </section>
            <!-- <section end here>-->
            <section>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-3">
                            <div class="labelCus" >Allow Attachments</div>
                        </div>
                        <div class="col-md-9"> <span class="radio">
                                <?php
                                $value = (!empty($errormsg) ? $this->request->data["Org"]["allow_attachment"] : 1);
                                $options = array(
                                    '1' => 'Yes',
                                    '0' => 'No'
                                );
                                echo $this->Form->input('allow_attachment', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3 col-xs-6">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => $value
                                        )
                                );
                                ?>
                            </span> </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-3">
                            <div class="labelCus">Leader Board Visible</div>
                        </div>
                        <div class="col-md-9"> <span class="radio">
                                <?php
                                $value = (!empty($errormsg) ? $this->request->data["Org"]["show_leader_board"] : 1);

                                $options = array(
                                    '1' => 'Yes',
                                    '0' => 'No'
                                );
                                echo $this->Form->input('show_leader_board', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3 col-xs-6">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => $value,
                                        )
                                );
                                ?>
                            </span> </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-3">
                            <div class="labelCus">Allow Comments</div>
                        </div>
                        <div class="col-md-9"> <span class="radio">
                                <?php
                                $value = (!empty($errormsg) ? $this->request->data["Org"]["allow_comments"] : 1);

                                $options = array(
                                    '1' => 'Yes',
                                    '0' => 'No'
                                );
                                echo $this->Form->input('allow_comments', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3 col-xs-6">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => $value,
                                    'class' => 'allowIt'
                                        )
                                );
                                ?>
                            </span> </div>
                    </div>

                    <div class="col-md-6  commentcompulsory"> 
                        <!--<div class="col-md-1"></div> -->
                        <div class="col-md-3">
                            <div class="labelCus" >Comment Optional</div>
                        </div>
                        <div class="col-md-9"> 
                            <span class="radio">
                                <?php
                                $value = (!empty($errormsg) ? $this->request->data["Org"]["optional_comments"] : 1);
                                $options = array(
                                    '1' => 'Yes',
                                    '0' => 'No'
                                );
                                echo $this->Form->input('optional_comments', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3 col-xs-6">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => $value
                                        )
                                );
                                ?>
                            </span> 
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 publicEndorse"> 
                        <!--<div class="col-md-1"></div> -->
                        <div class="col-md-3">
                            <div class="labelCus">Public nDorsement Comment Visible on Live Feed</div>
                        </div>
                        <div class="col-md-9"> 
                            <span class="radio">
                                <?php
                                $value = (!empty($errormsg) ? $this->request->data["Org"]["public_endorse_visible"] : 1);
                                $options = array(
                                    '1' => 'Yes',
                                    '0' => 'No'
                                );
                                echo $this->Form->input('public_endorse_visible', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3 col-xs-6">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => $value,
                                    'class' => 'endorse-visible-alert',
                                        )
                                );
                                ?>
                            </span> 
                            <div class="labelCus" id="endorse_visible_alert" style="color: salmon;">
                                <div style="float: left;height: 60px;"><?php echo $this->Html->image("Alert_Symbol.png", array('height' => "20px", 'width' => "20px")); ?></div>
                                <div>
                                    *By choosing this feature, comments in Public nDorsements that appear of LiveFeed will be visible to all users within the Organization. 
                                    nDorse App does not provide a mechanism for Admins to delete nDorsements from LiveFeed.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-3">
                            <div class="labelCus require">Country</div>
                        </div>
                        <div class="col-md-9">
                            <div class="select-style"> <?php echo $this->Form->input('country', array('empty' => 'Select Country', 'default' => '', 'selected' => $country_code, 'class' => 'form-control country', 'label' => false, 'options' => $listCountries, 'data-url' => Router::url(array('controller' => 'ajax', 'action' => 'states')))); ?> </div>
                        </div>
                    </div>
                    <div class="col-md-6"> 
                        <!--<div class="col-md-1"></div> -->
                        <div class="col-md-3">
                            <div class="labelCus require">State</div>
                        </div>
                        <div class="col-md-9">
                            <div>
                                <?php
                                $stylestatelist = "style=display:block;";
                                $stylestatetextlist = "style=display:none;";
//pr($listState);
                                if ((empty($listState))) {
                                    $stylestatelist = "style=display:none;";
                                    $stylestatetextlist = "style=display:block;";
                                }
                                ?>
                                <div class="select-style" id="selectstate" <?php echo $stylestatelist; ?> > <?php echo $this->Form->input('state', array('empty' => 'Select State', 'label' => false, 'options' => $listState, 'selected' => $stateselected, 'class' => 'form-control states')); ?> </div>
                                <div id="selectstatetext" <?php echo $stylestatetextlist; ?> > <?php echo $this->Form->input('state_name', array('type' => 'text', 'class' => 'textbox', 'id' => 'state_name', 'label' => false)); ?> </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!--ends here-->
            <section>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-3">
                            <div class="labelCus require">City</div>
                        </div>
                        <div class="col-md-9"> <?php echo $this->Form->input('city', array('placeholder' => 'City', 'class' => 'form-control', 'label' => false, 'type' => 'text')); ?> </div>
                    </div>
                    <div class="col-md-6"> 
                        <!--<div class="col-md-1"></div> -->
                        <div class="col-md-3">
                            <div class="labelCus require">Street</div>
                        </div>
                        <div class="col-md-9"> <?php echo $this->Form->input('street', array('placeholder' => 'Street', 'class' => 'form-control', 'label' => false, 'type' => 'text')); ?> </div>
                    </div>
                </div>
            </section>
            <section>
                <div class="row">
                    <div class="col-md-6">
                        <div class="col-md-3">
                            <div class="labelCus require">Zip code</div>
                        </div>
                        <div class="col-md-9"> <?php echo $this->Form->input('zip', array('placeholder' => 'Zip', 'class' => 'form-control txt-decimal', 'label' => false, 'type' => 'text')); ?> </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-3">
                            <div class="labelCus require">Phone number</div>
                        </div>
                        <div class="col-md-9"> <?php echo $this->Form->input('phone_number', array('placeholder' => 'Phone Number', 'class' => 'form-control txt-decimal', 'label' => false, 'type' => 'text', "onkeypress" => "return isNumberKey(event);")); ?> </div>
                    </div>
                </div>
            </section>
        </div>
    </section>
    <section class="AddCoreValue">
        <div class="row AddCoreValue">
            <div class="col-md-12">
                <div class="pull-left">
                    <h4 class="require">ENTER CORE VALUES / SET GOALS</h4>
                </div>
                <div class="pull-right"><span>Add a New Core Value</span> <a href="javascript:void(0);"><?php echo $this->Html->image('addCoreValue.png', array('id' => 'addcorevalues')); ?></a></div>
            </div>
        </div>
        <div class="row CreateEditOrg" id="corevaluetable">
            <table id="addcoretable" class="table table-responsive table-hover" >
                <tr>
                    <th>Active</th>
                    <th>Core value</th>
                    <th>Color Code</th>
                    <th><div class="EditSave">Edit / Save</div></th>
                    <th><div class="CustomMessage">Custom Message</div></th>
                    <th>Delete</th>
                </tr>
                <?php
                if (!empty($resultantdepartment["corevalues"])) {
                    for ($i = 0; $i < count($resultantdepartment["corevalues"]["value"]); $i++) {
                        $dept = $this->request->data['Org']['corevalues'];
                        ?>
                        <tr id="addcorerow_<?php echo $i + 1; ?>">
                            <td ><?php //echo $this->Form->checkbox('cvactivestatus', array('hiddenField' => false, "checked" => "checked", "name"=> "data[Org][cvactivestatus][]"));        ?>
                                <div class="checkbox">
                                    <input type="checkbox" class="OrgCvactivestatus css-checkbox" id="cvid_1" checked="checked">
                                    <label for="cvid_1" class="css-label"></label>
                                </div>
                                <input type="hidden" id="cvactive" name="data[Org][cvactive][]" value="<?php echo $resultantdepartment["corevalues"]["activestatus"][$i]; ?>">
                                <input type="hidden" id="saveunsave" class="cvsaveunsave" name="data[Org][save][]" value="<?php echo $resultantdepartment["corevalues"]["savestatus"][$i]; ?>"></td>
                            <td id="corevaluesdropdown"><div class = "select-style">
                                    <?php $style = ($resultantdepartment["corevalues"]["savestatus"][$i] == "save") ? "none" : "block"; ?>
                                    <select class="form-control" id="OrgCorevalues" name="data[Org][corevalues][]" id="" style="display: <?php echo $style; ?>">
                                        <option value="">Select Core Values</option>
                                        <?php
                                        foreach ($corevalues as $corevalue) {
                                            $selected = ($corevalue == $dept[$i]) ? "selected=selected" : "";
                                            echo '<option value="' . $corevalue . '" ' . $selected . '>' . $corevalue . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <input id="OrgOtherDepartment" type="hidden" name="data[Org][other_department][]" value="<?php echo $resultantdepartment["corevalues"]["value"][$i]; ?>" class="form-control other_department">
                                <?php
                                if ($resultantdepartment["corevalues"]["savestatus"][$i] == "save") {
                                    echo "<p id = 'corevalue'>" . $resultantdepartment["corevalues"]["value"][$i] . "</p>";
                                }
                                ?></td>
                            <td ><?php
                                $colorvalue = $resultantdepartment["corevalues"]["colorcode"][$i];
                                ?>
                                <input type="hidden" name="data[Org][cp][]" value="<?php echo $colorvalue; ?>">
                                <input type="text" style="color: <?php echo $colorvalue; ?>; background-color: #001e52"  value="Hello" id="colorpick"/></td>
                            <td id="savecv"><?php echo $this->Html->image('EditRow.png', array('id' => 'editcorevalues')); ?> / <?php echo $this->Html->image('SaveRow.png', array('class' => 'savecorevalues')); ?></td>
                            <td><div class="CustomMessage">Custom Message</div></td>
                            <td id="deletecv"><?php echo $this->Html->image('DeleteRow.png', array('id' => 'deletecorevalues')); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr id="addcorerow_1">
                        <td ><?php
                            //echo $this->Form->checkbox('cvactivestatus', array('hiddenField' => false, "checked" => "checked", "name"=> "data[Org][cvactivestatus][]")); 
                            ?>
                            <div class="checkbox">
                                <input type="checkbox" class="OrgCvactivestatus css-checkbox" id="cvid_1" checked="checked">
                                <label for="cvid_1" class="css-label"></label>
                            </div>

                            <!--<input type="checkbox" class="OrgCvactivestatus" id="OrgCvactivestatusid" checked="checked"> -->

                            <input type="hidden" id="cvactive" name="data[Org][cvactive][]" value="active">
                            <input type="hidden" id="saveunsave" class="cvsaveunsave" name="data[Org][save][]" value="unsave"></td>
                        <td id="corevaluesdropdown" ><div class="select-style" style=""> <?php echo $this->Form->input('corevalues', array('empty' => 'Select Core Value', 'label' => false, 'options' => $corevalues, 'class' => 'form-control', 'name' => 'data[Org][corevalues][]')); ?> </div>
                            <div id="othercv" style="display:none"> <?php echo $this->Form->input('other_department', array('id' => 'OrgOtherDepartment', 'class' => 'form-control', 'label' => false, 'name' => 'data[Org][other_department][]')); ?> </div></td>
                        <td ><input type="hidden" name="data[Org][cp][]" value="#FFFFFF">
                            <input type="text" style="color: #FFFFFF; background-color: #001e52"  value="Hello" id="colorpick"/>

                            <!--                        <input name="data[Org][cp][]" placeholder="Select Color Code" id="colorpick" readonly="readonly" type="color" value="" />--></td>
                        <td  id="savecv"><div class="EditSave"><?php echo $this->Html->image('EditRow.png', array('id' => 'editcorevalues')); ?> / <?php echo $this->Html->image('SaveRow.png', array('class' => 'savecorevalues')); ?></div></td>
                        <td><div class="CustomMessage">Custom Message</div></td>
                        <td  id="deletecv"><?php echo $this->Html->image('DeleteRow.png', array('id' => 'deletecorevalues')); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </section>
    <section class="AddCoreValue">
        <div class="row AddCoreValue">
            <div class="col-md-12">
                <div class="pull-left">
                    <h4>ENTER SUB ORGANIZATION</h4>
                </div>
                <div class="pull-right"><span>Add a New Sub Organization</span> <a href="javascript:void(0);"><?php echo $this->Html->image('addCoreValue.png', array('id' => 'addentity')); ?></a> </div>
            </div>
        </div>
        <div class="row CreateEditOrg" id="addentitydiv">
            <table class="table table-hover" id="addentitytable" >
                <tr>
                    <th>Active</th>
                    <th>Sub Organization</th>
                    <th><div class="EditSave">Edit / Save</div></th>
                    <th>Delete</th>
                </tr>
                <?php
                if (!empty($resultantdepartment["entity"])) {
                    for ($i = 0; $i < count($resultantdepartment["entity"]["value"]); $i++) {
                        ?>
                        <tr id="addentity_<?php echo $i + 1; ?>">
                            <td ><div class="checkbox">
                                    <input id="entityid_1" type="checkbox" name="entirycheckbox" checked class="entitycheckbox css-checkbox" />
                                    <label class="css-label" for="entityid_1"></label>
                                </div>
                                <input type="hidden" class="entityactive" name="data[Org][entityactive][]" value="<?php echo $resultantdepartment["entity"]["activestatus"][$i]; ?>">
                                <input type="hidden" class="entitysaveunsave" name="data[Org][entitysave][]" value="<?php echo $resultantdepartment["entity"]["savestatus"][$i]; ?>"></td>
                            <?php $style = ($resultantdepartment["entity"]["savestatus"][$i] == "save") ? "none" : "block"; ?>
                            <td class="entityvaluestextbox"><input placeholder="Add Sub Organization" value="<?php echo $resultantdepartment["entity"]["value"][$i]; ?>" class="entitytextbox" type="textbox" name="data[Org][entitytextbox][]" class="entitytextbox" style="display:<?php echo $style; ?>" />
                                <?php
                                if ($resultantdepartment["entity"]["savestatus"][$i] == "save") {
                                    echo "<p class = 'corevalue'>" . $resultantdepartment["entity"]["value"][$i] . "</p>";
                                }
                                ?></td>
                            <td ><div class="EditSave"><?php echo $this->Html->image("EditRow.png", array("class" => "editentity")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "saveentity")); ?></div></td>
                            <td ><?php echo $this->Html->image("DeleteRow.png", array("class" => "deleteentity")); ?></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr id="addentity_1">
                        <td ><div class="checkbox">
                                <input type="checkbox" name="entirycheckbox" checked class="entitycheckbox css-checkbox" id="entityid_1" />
                                <label class="css-label" for="entityid_1"></label>
                            </div>
                            <input type="hidden" class="entityactive" name="data[Org][entityactive][]" value="active">
                            <input type="hidden" class="entitysaveunsave" name="data[Org][entitysave][]" value="unsave"></td>
                        <td class="entityvaluestextbox"><input placeholder="Add Sub Organization" class="entitytextbox" type="textbox" name="data[Org][entitytextbox][]" class="entitytextbox" /></td>
                        <td ><div class="EditSave"><?php echo $this->Html->image("EditRow.png", array("class" => "editentity")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "saveentity")); ?></div></td>
                        <td ><?php echo $this->Html->image("DeleteRow.png", array("class" => "deleteentity")); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </section>

    <!-- Add Departments-->
    <section class="AddCoreValue">
        <div class="row AddCoreValue">
            <div class="col-md-12">
                <div class="pull-left">
                    <h4>ENTER DEPARTMENTS</h4>
                </div>
                <div class="pull-right"><span>Add a New Department</span><a href="javascript:void(0);"><?php echo $this->Html->image("addCoreValue.png", array('id' => 'adddepartment')); ?></a></div>
            </div>
        </div>
        <div class="row CreateEditOrg" id="adddepartmentdiv">
            <table class="table table-hover" id="adddepartmenttable">
                <tr>
                    <th >Active</th>
                    <th >Department</th>
                    <th ><div class="EditSave">Edit / Save</div></th>
                    <th >Delete</th>
                </tr>
                <?php
                if (!empty($resultantdepartment["department"])) {
                    for ($i = 0; $i < count($resultantdepartment["department"]["value"]); $i++) {
                        $dept = $this->request->data['Org']['departments'];
                        ?>
                        <tr id="adddepartment_<?php echo $i + 1; ?>">
                            <td ><div class="checkbox">
                                    <input type="checkbox" name="departmentcheckbox" checked class="entitycheckbox css-checkbox" id="departmentid_1" />
                                    <label class="css-label" for="departmentid_1"></label>
                                </div>
                                <input type="hidden" class="departmentactive" name="data[Org][departmentactive][]" value="<?php echo $resultantdepartment["department"]["activestatus"][$i]; ?>">
                                <input type="hidden" class="departmentsaveunsave" name="data[Org][departmentsave][]" value="<?php echo $resultantdepartment["department"]["savestatus"][$i]; ?>"></td>
                            <td class="departmentselectrow"><div class = "select-style">
                                    <?php $style = ($resultantdepartment["department"]["savestatus"][$i] == "save") ? "none" : "block"; ?>
                                    <select class="departmentvalues form-control" name="data[Org][departments][]" id="" style="display: <?php echo $style; ?>">
                                        <option value="">Select Department</option>
                                        <?php
                                        foreach ($departments as $department) {
                                            $selected = ($department == $dept[$i]) ? "selected=selected" : "";
                                            echo '<option value="' . $department . '" ' . $selected . '>' . $department . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <input type="hidden" name="data[Org][department_other_department][]" value="<?php echo $resultantdepartment["department"]["value"][$i]; ?>" class="form-control other_department">
                                <?php
                                if ($resultantdepartment["department"]["savestatus"][$i] == "save") {
                                    echo "<p class = 'departmentv'>" . $resultantdepartment["department"]["value"][$i] . "</p>";
                                }
                                ?></td>
                            <td ><div class="EditSave"><?php echo $this->Html->image("EditRow.png", array("class" => "editdepartment")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savedepartment")); ?></div></td>
                            <td ><?php echo $this->Html->image("DeleteRow.png", array("class" => "deletedepartment")); ?></td>
                        </tr>
                    <?php }
                    ?>
                <?php } else { ?>
                    <tr id="adddepartment_1">
                        <td><div class="checkbox">
                                <input type="checkbox" name="departmentcheckbox" checked class="entitycheckbox css-checkbox" id="departmentid_1" />
                                <label class="css-label" for="departmentid_1"></label>
                            </div>
                            <input type="hidden" class="departmentactive" name="data[Org][departmentactive][]" value="active">
                            <input type="hidden" class="departmentsaveunsave" name="data[Org][departmentsave][]" value="unsave"></td>
                        <td class="departmentselectrow"><div class = "select-style">
                                <select class="departmentvalues form-control" name="data[Org][departments][]" id="">
                                    <option value="">Select Department</option>
                                    <?php
                                    foreach ($departments as $department) {
                                        echo '<option value="' . $department . '">' . $department . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <input type="hidden" name="data[Org][department_other_department][]" class="form-control other_department"></td>
                        <td><div class="EditSave"><?php echo $this->Html->image("EditRow.png", array("class" => "editdepartment")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savedepartment")); ?></div></td>
                        <td><?php echo $this->Html->image("DeleteRow.png", array("class" => "deletedepartment")); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </section>

    <!-- Add Job Title-->
    <section class="AddCoreValue">
        <div class="row AddCoreValue">
            <div class="col-md-12">
                <div class="pull-left">
                    <h4>ENTER JOB TITLE</h4>
                </div>
                <div class="pull-right"><span>Add a New job Title</span><a href="javascript:void(0);"><?php echo $this->Html->image("addCoreValue.png", array('id' => 'addjobtitle')); ?></a></div>
            </div>
        </div>
        <div class="row CreateEditOrg" id="addjobtitlediv">
            <table class="table table-hover" id="addjobtitletable">
                <tr>
                    <th >Active</th>
                    <th >Job Title</th>
                    <th ><div class="EditSave">Edit / Save</div></th>
                    <th >Delete</th>
                </tr>
                <?php
                if (!empty($resultantdepartment["jobtitle"])) {
                    for ($i = 0; $i < count($resultantdepartment["jobtitle"]["value"]); $i++) {
                        $dept = $this->request->data['Org']['jobtitle'];
                        ?>
                        <tr id="addjobtitle_<?php echo $i + 1; ?>">
                            <td ><div class="checkbox">
                                    <input type="checkbox" name="jobtitlecheckbox" id="jbactive_1" checked class="jobtitlecheckbox css-checkbox" />
                                    <label class="css-label" for="jbactive_1"></label>
                                </div>
                                <input type="hidden" class="jobtitleactive" name="data[Org][jobtitleactive][]" value="<?php echo $resultantdepartment["jobtitle"]["activestatus"][$i]; ?>">
                                <input type="hidden" class="jobtitlesaveunsave" name="data[Org][jobtitlesave][]" value="<?php echo $resultantdepartment["jobtitle"]["savestatus"][$i]; ?>"></td>
                            <td class="jobtitleselectrow"><div class = "select-style">
                                    <?php $style = ($resultantdepartment["jobtitle"]["savestatus"][$i] == "save") ? "none" : "block"; ?>
                                    <select class="jobtitlevalues form-control" name="data[Org][jobtitle][]" id="" style="display: <?php echo $style; ?>">
                                        <option value="">Select job Title</option>
                                        <?php
                                        foreach ($jobtitles as $jobtitle) {
                                            $selected = ($jobtitle == $dept[$i]) ? "selected=selected" : "";
                                            echo '<option value="' . $jobtitle . '" ' . $selected . '>' . $jobtitle . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <input type="hidden" name="data[Org][jobtitle_other_department][]" value="<?php echo $resultantdepartment["jobtitle"]["value"][$i]; ?>" class="form-control other_jobtitle">
                                <?php
                                if ($resultantdepartment["jobtitle"]["savestatus"][$i] == "save") {
                                    echo "<p class = 'jobtitlev'>" . $resultantdepartment["jobtitle"]["value"][$i] . "</p>";
                                }
                                ?></td>
                            <td ><div class="EditSave"><?php echo $this->Html->image("EditRow.png", array("class" => "editjobtitle")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savejobtitle")); ?></div></td>
                            <td ><?php echo $this->Html->image("DeleteRow.png", array("class" => "deletejobtitle")); ?></td>
                        </tr>
                    <?php }
                    ?>
                <?php } else { ?>
                    <tr id="addjobtitle_1">
                        <td ><div class="checkbox">
                                <input type="checkbox" id="jbactive_1" name="jobtitlecheckbox" checked class="jobtitlecheckbox css-checkbox" />
                                <label class="css-label" for="jbactive_1"></label>
                            </div>

                            <!--<input type="checkbox" name="jobtitlecheckbox" checked class="jobtitlecheckbox" /> -->

                            <input type="hidden" class="jobtitleactive" name="data[Org][jobtitleactive][]" value="active">
                            <input type="hidden" class="jobtitlesaveunsave" name="data[Org][jobtitlesave][]" value="unsave"></td>
                        <td class="jobtitleselectrow"><div class = "select-style">
                                <select class="jobtitlevalues form-control" name="data[Org][jobtitle][]" id="">
                                    <option value="">Select Job Title</option>
                                    <?php
                                    foreach ($jobtitles as $jobtitle) {
                                        echo '<option value="' . $jobtitle . '">' . $jobtitle . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <input type="hidden" name="data[Org][jobtitle_other_department][]" class="form-control other_jobtitle"></td>
                        <td ><div class="EditSave"><?php echo $this->Html->image("EditRow.png", array("class" => "editjobtitle")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savejobtitle")); ?></div></td>
                        <td ><?php echo $this->Html->image("DeleteRow.png", array("class" => "deletejobtitle")); ?></td>
                    </tr>
                <?php } ?>
            </table>
            <div class="pull-left">
                <button type="button" class="btn btn-orange" id="orgformsubmit">Create</button>
                <!--        <button type="button" class="btn btn-orange" id="orgformcancel">Cancel</button>-->
            </div>
        </div>
    </section>
    <!--<section class="container-fluid footer-bg">
      <div class="container">
          <div class="row">
              <div class="pull-right">
                  <button type="button" class="btn btn-default" id="orgformcancel">Cancel</button>
                  <button type="button" class="btn btn-default" id="orgformsubmit">Save</button>
              </div>
          </div>
      </div>
  </section> --> 

    <?php echo $this->Form->end(); ?> </div>
<script>

    $(document).ready(function () {
        var allow_comment = "<?php echo isset($this->request->data["Org"]["allow_comments"]) ? $this->request->data["Org"]["allow_comments"] : 1; ?>";
        console.log(allow_comment);
        if (allow_comment == 1) {
            $('.commentcompulsory').show();
        } else {
            $('.commentcompulsory').hide();
        }
    });

    $('.endorse-visible-alert').on('click', function () {
        if ($('input[class=endorse-visible-alert]:checked').val() == 1) {
            $('#endorse_visible_alert').fadeIn('medium');

        } else {
            $('#endorse_visible_alert').fadeOut('medium');

        }
    });

    $('.allowIt').on('click', function () {
        if ($('input[class=allowIt]:checked').val() == 1) {
            $('.commentcompulsory').slideDown('medium');
            $(".publicEndorse").slideDown('medium');
        } else {
            $('.commentcompulsory').slideUp('medium');
            $(".publicEndorse").slideUp('medium');
        }
    });

    var rowlength = $("#addcoretable tr").length;
    for (var i = 1; i < rowlength; i++) {
        var demo1 = $('#addcorerow_' + i + ' #colorpick');
        demo1.colorpickerplus();
        demo1.on('changeColor', function (e, color) {
            if (color == null)
                $(this).val('transparent').css('background-color', '#FFFFFF');//tranparent
            else
                $(this).val("Hello").css('color', color);
            $($(this).parent().find("input[type ='hidden']")[0]).val(color);
            $(this).css('background-color', "#001e52");
        });
    }
</script> 

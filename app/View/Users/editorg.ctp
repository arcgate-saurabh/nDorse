<?php
echo $this->Html->script("bootstrap-colorpicker.min.js");
echo $this->Html->script("bootstrap-colorpicker-plus.js"); //5 hrs
echo $this->Html->css("bootstrap-colorpicker.min.css");
echo $this->Html->css("bootstrap-colorpicker-plus.css");
$resultantdepartment = '';
if (!empty($errormsg)) {
    $resultantdepartment["department"] = array("value" => array(), "hiddenid" => array(), "activestatus" => array(), "savestatus" => array());
    $resultantdepartment["jobtitle"] = array("value" => array(), "hiddenid" => array(), "activestatus" => array(), "savestatus" => array());
    $resultantdepartment["entity"] = array("value" => array(), "hiddenid" => array(), "activestatus" => array(), "savestatus" => array());
    $resultantdepartment["corevalues"] = array("value" => array(), "hiddenid" => array(), "colorcode" => array(), "activestatus" => array(), "savestatus" => array());
    $array = array("department", "jobtitle", "entity");
    $hiddenSubCenterArray = array();
    foreach ($array as $orgmenus) {
//        $valuearray = array();
        $valuearray = $hiddenidarray = $savestatusarray = $activestatusarray = array();
//        $savestatusarray = array();
//        $hiddenidarray = array();
        $org = $orgmenus . 'active';
        if (($org != "")) {
            if (!empty($this->request->data['Org'][$orgmenus . 'active'])) {
                $counterdepartments = count($this->request->data['Org'][$orgmenus . 'active']);
                for ($i = 0; $i < $counterdepartments; $i++) {
                    $change = ($orgmenus == "department") ? "departments" : "jobtitles";
                    if ($orgmenus == "entity") {
                        $value = $this->request->data['Org']["entitytextbox"][$i];
                    } else {
                        $value = $this->request->data['Org'][$change][$i];
                    }
                    $hiddenid = isset($this->request->data['Org'][$orgmenus . 'hiddenid'][$i]) ? $this->request->data['Org'][$orgmenus . 'hiddenid'][$i] : "";
                    $activestatus = $this->request->data['Org'][$orgmenus . 'active'][$i];
                    $savestatus = $this->request->data['Org'][$orgmenus . 'save'][$i];
                    if ($value == "other") {
                        $value = $this->request->data['Org'][$orgmenus . '_other_department'][$i];
                    }
                    array_push($valuearray, $value);
                    array_push($hiddenidarray, $hiddenid);
                    array_push($activestatusarray, $activestatus);
                    array_push($savestatusarray, $savestatus);
                }
                $resultantdepartment[$orgmenus]["value"] = $valuearray;
                $resultantdepartment[$orgmenus]["hiddenid"] = $hiddenidarray;
                $resultantdepartment[$orgmenus]["activestatus"] = $activestatusarray;
                $resultantdepartment[$orgmenus]["savestatus"] = $savestatusarray;
            }
        }
    }
    //=====================for corevalues
    $countercorevalues = count($this->request->data['Org']['cvactive']);
    $valuearray = array();
    $activestatusarray = array();
    $savestatusarray = array();
    $savecolorcodearray = array();
    $hiddenidarray = array();
    for ($i = 0; $i < $countercorevalues; $i++) {
        $value = $this->request->data['Org']["corevalues"][$i];
        $activestatus = $this->request->data['Org']['cvactive'][$i];
        $savestatus = $this->request->data['Org']['save'][$i];
        $hiddenid = isset($this->request->data['Org']['hiddenid'][$i]) ? $this->request->data['Org']['hiddenid'][$i] : "";
        if ($value == "other") {
            $value = $this->request->data['Org']['other_department'][$i];
        }
        $colorcodes = $this->request->data['Org']['cp'][$i];
        array_push($valuearray, $value);
        array_push($activestatusarray, $activestatus);
        array_push($savestatusarray, $savestatus);
        array_push($savecolorcodearray, $colorcodes);
        array_push($hiddenidarray, $hiddenid);
    }
    $resultantdepartment["corevalues"]["value"] = $valuearray;
    $resultantdepartment["corevalues"]["activestatus"] = $activestatusarray;
    $resultantdepartment["corevalues"]["savestatus"] = $savestatusarray;
    $resultantdepartment["corevalues"]["colorcode"] = $savecolorcodearray;
    $resultantdepartment["corevalues"]["hiddenid"] = $hiddenidarray;
    //pr($resultantdepartment);
}
?>
<?php ?>
<script>
    //===============variable to be used in editing
    var js_arraycv = <?php echo json_encode($corevalues); ?>;
    var js_arraydept = <?php echo json_encode($departments); ?>;
    var js_arrayjt = <?php echo json_encode($jobtitles); ?>;

    $(document).ready(function () {
        var corevalues = '<?php echo json_encode($corevalues); ?>';
        $('#addcorevalues').bind("click", function () {
            if ($('#corevaluetable').is(":visible")) {
                var cvalues = JSON.parse(corevalues);
                //var rowlength = $("#addcoretable tr").length;
                var rowlength = parseInt($("#addcoretable tr:last").attr("id").split("_")[1]) + 1;
                var rowlengthoriginal = parseInt($("#addcoretable tr:last").attr("id").split("_")[1]);
                $('<tr id="addcorerow_' + rowlength + '">').appendTo("#addcoretable");

                $('<td><div class="checkbox"><input type="checkbox" id="cvfordaisy_' + rowlength + '" class="css-checkbox" value="1" name="data[Org][cvfordaisy][]"><label for="cvfordaisy_' + rowlength + '" class="css-label"></label></div></td>').appendTo("#addcorerow_" + rowlength);
                $('<td><div class="checkbox"><input type="checkbox" id="cvforweb_' + rowlength + '" class="css-checkbox" value="1" name="data[Org][cvforweb][]"><label for="cvforweb_' + rowlength + '" class="css-label"></label></div></td>').appendTo("#addcorerow_" + rowlength);
                $('<td><div class="checkbox"><input type="checkbox" id="cvforguest_' + rowlength + '" class="css-checkbox" value="1" name="data[Org][cvforguest][]"><label for="cvforguest_' + rowlength + '" class="css-label"></label></div></td>').appendTo("#addcorerow_" + rowlength);

                $('<td><div class="checkbox"><input id="cvid_' + rowlength + '" type="checkbox" class="OrgCvactivestatus css-checkbox" value="1" checked="checked" name="data[Org][cvactivestatus][]"><label for="cvid_' + rowlength + '" class="css-label"></label></div><input type="hidden" id="cvactive" name="data[Org][cvactive][]" value="active"><input id="saveunsave" type="hidden" value="save" name="data[Org][save][]"></td>').appendTo("#addcorerow_" + rowlength);
                $('<td id="corevaluesdropdown"><div class="input select"><div class = "select-style"><select id="OrgCorevalues" class="form-control" name="data[Org][corevalues][]"><option value="">Select Core Value</option></select></div></div><div style="display:none" id="othercv"><div class="input text"><input type="text" id="OrgOtherDepartment" class="form-control" name="data[Org][other_department][]"></div></div></td>').appendTo("#addcorerow_" + rowlength);
                $('<td><input type="hidden" name="data[Org][cp][]" value="#FFFFFF"><input style="color:#FFFFFF; background-color:#001e52" type="text" value="Hello" readonly="readonly" id="colorpick" class="color-picker-binded"></td>').appendTo("#addcorerow_" + rowlength);
                $('<td id="savecv"><?php echo $this->Html->image("EditRow.png", array("id" => "editcorevalues")); ?> <?php echo $this->Html->image("SaveRow.png", array("class" => "savecorevalues")); ?></td>').appendTo("#addcorerow_" + rowlength);
                $('<td class="custm-msg"><div class="checkbox"><input type="checkbox" id="customeditid_' + rowlengthoriginal + '" class="customCoreMessage css-checkbox" value="1" checked="checked" name="data[Org][custom_message_enabled][' + rowlengthoriginal + ']"><label for="customeditid_' + rowlengthoriginal + '" class="css-label"></label></div><textarea class="custommsgattrtextarea" name="data[Org][custom_message_text][' + rowlengthoriginal + ']"> </textarea></td>').appendTo("#addcorerow_" + rowlength);
                $('<td id="deletecv"><img alt="" id="deletecorevalues" src="' + siteurl + 'img/DeleteRow.png"></td>').appendTo("#addcorerow_" + rowlength);
                $('</tr>').appendTo("#addcorerow_" + rowlength);
                for (tmpvalues in cvalues) {
                    $('<option value="' + cvalues[tmpvalues] + '">' + cvalues[tmpvalues] + '</option>').appendTo("#addcorerow_" + rowlength + " #OrgCorevalues");
                }
                var demo1 = $('#addcorerow_' + rowlength + ' #colorpick');
                demo1.colorpickerplus();
                demo1.on('changeColor', function (e, color) {
                    if (color == null)
                        $(this).val('transparent').css('background-color', '#FFFFFF');//tranparent
                    else
                        $(this).val("Hello").css('color', color);
                    $($(this).parent().find("input[type ='hidden']")[0]).val(color);
                    $(this).css('background-color', "#001e52");
                    //$(this).val(color).css('background-color', color);
                });
            }
            $("#corevaluetable").show();
        });
        //Added by Babulal Prasad @26-Nov-2019
        $('#addsubcenter').bind("click", function () {
            if ($('#subcentertable').is(":visible")) {
                var cvalues = JSON.parse(corevalues);
                var rowlength = parseInt($("#addsubcentertable tr:last").attr("id").split("_")[1]) + 1;
                var rowlengthoriginal = parseInt($("#addsubcentertable tr:last").attr("id").split("_")[1]);
                $('<tr id="addcenterrow_' + rowlength + '">').appendTo("#addsubcentertable");
                $('<td class="custm-msg"><input type="hidden" value="" name="data[Org][subcenter_id][]"><input class="subcenterlongvalue form-control" type = "text" name = "data[Org][subcenter_long][]" value = "" style="display:block;" /><p class="sub_longname" style="display:none;"></p><input type="hidden" id="SCactive" name="data[Org][SCactive][]" value="active"><input type="hidden" id="SCsaveunsave" name="data[Org][SCsave][]" value="unsave"><input type="hidden" id="SChiddenid" name="data[Org][SChiddenid][]" value=""></td>').appendTo("#addcenterrow_" + rowlength);
                $('<td class="custm-msg"><input class="subcentershortvalue form-control" type = "text" name = "data[Org][subcenter_short][]" value = "" style="display:block;"/><p class="sub_shortname" style="display:none;"></p></td>').appendTo("#addcenterrow_" + rowlength);
                $('<td width="20%"><?php echo $this->Html->image("EditRow.png", array("id" => "editsubcenter")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savesubcenter")); ?></td>').appendTo("#addcenterrow_" + rowlength);
                $('<td width="20%"><?php echo $this->Html->image("DeleteRow.png", array("id" => "deletesubcenter")); ?></td>').appendTo("#addcenterrow_" + rowlength);
                $('</tr>').appendTo("#addcenterrow_" + rowlength);
            }
            $("#subcentertable").show();
        });
        //Added by Babulal Prasad @26-Nov-2019
        $('#addhashtag').bind("click", function () {
            if ($('#hashtagtable').is(":visible")) {
                var cvalues = JSON.parse(corevalues);
                var rowlength = parseInt($("#addhashtagtable tr:last").attr("id").split("_")[1]) + 1;
                var rowlengthoriginal = parseInt($("#addhashtagtable tr:last").attr("id").split("_")[1]);
                $('<tr id="addhashtagrow_' + rowlength + '">').appendTo("#addhashtagtable");
                $('<td class="custm-msg"><input type="hidden" value="" name="data[Org][hashtag_id][]"><input class="hashtagvalues form-control" type = "text" name = "data[Org][hashtag][]" value = "" style="display: block;" /><p class="corevalue" style="display: none"></p><input type="hidden" id="HTactive" name="data[Org][HTactive][]" value="active"></p><input type="hidden" id="HTsaveunsave" name="data[Org][HTsave][]" value="unsave"><input type="hidden" id="HThiddenid" name="data[Org][HThiddenid][]" value=""></td>').appendTo("#addhashtagrow_" + rowlength);
                $('<td width="20%"><?php echo $this->Html->image("EditRow.png", array("id" => "edithashtag")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savehashtag")); ?></td>').appendTo("#addhashtagrow_" + rowlength);
                $('<td width="20%"><?php echo $this->Html->image("DeleteRow.png", array("id" => "deletehashtag")); ?></td>').appendTo("#addhashtagrow_" + rowlength);
                $('</tr>').appendTo("#addhashtagrow_" + rowlength);
            }
            $("#hashtagtable").show();
        });

        //department
        $(document).on('click', '#adddepartment', function () {

            //var rowlength = $("#adddepartmentable tr:visible").length;
            if (typeof ($("#adddepartmenttable tr:last").attr("id")) == "undefined") {
                $("#adddepartmentdiv").show();
                var rowlength = 1;
            } else {
                var rowlength = parseInt($("#adddepartmenttable tr:last").attr("id").split("_")[1]) + 1;
            }
            console.log(rowlength);
            //if (rowlength <= 0 || $('#adddepartmentdiv').is(":visible")) {
            var departmentvalues = '<?php echo json_encode($departments); ?>';
            var dvalues = JSON.parse(departmentvalues);
            //var rowlength = $("#adddepartmenttable tr").length;
            $('<tr id="adddepartment_' + rowlength + '">').appendTo("#adddepartmenttable");
            $('<td><div class="checkbox"><input type="checkbox" id="departmentid_' + rowlength + '" class="departmentcheckbox css-checkbox" checked="" name="departmentcheckbox"><label for="departmentid_' + rowlength + '" class="css-label"></label></div><input type="hidden" value="active" name="data[Org][departmentactive][]" class="departmentactive"><input type="hidden" value="unsave" name="data[Org][departmentsave][]" class="departmentsaveunsave"></td>').appendTo("#adddepartment_" + rowlength);
            $('<td class="departmentselectrow"><div class = "select-style"><select name="data[Org][departments][]" class="departmentvalues form-control valid"><option value="">Select Department</option></select></div><input type="hidden" name="data[Org][department_other_department][]" class="form-control other_department"></td>').appendTo("#adddepartment_" + rowlength);
            $('<td><?php echo $this->Html->image("EditRow.png", array("class" => "editdepartment")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savedepartment")); ?></td>').appendTo("#adddepartment_" + rowlength);
            $('<td><?php echo $this->Html->image("DeleteRow.png", array("class" => "deletedepartment")); ?></td>').appendTo("#adddepartment_" + rowlength);
            $('</tr>').appendTo("#adddepartment_" + rowlength);
            for (tmpvalues in dvalues) {
                $('<option value="' + dvalues[tmpvalues] + '">' + dvalues[tmpvalues] + '</option>').appendTo("#adddepartment_" + rowlength + " .departmentvalues");
            }
//            }
//            $("#adddepartmentdiv").show();
        });

        //Job title
        $(document).on('click', '#addjobtitle', function () {
            //var rowlength = $("#addjobtitletable tr:visible").length;
            if (typeof ($("#addjobtitletable tr:last").attr("id")) == "undefined") {
                $("#addjobtitlediv").show();
                var rowlength = 1;
            } else {
                var rowlength = parseInt($("#addjobtitletable tr:last").attr("id").split("_")[1]) + 1;
            }

            //if (rowlength <= 0 || $('#addjobtitlediv').is(":visible")) {
            var jobtitlevalues = '<?php echo json_encode($jobtitles); ?>';
            var jvalues = JSON.parse(jobtitlevalues);
            var rowlength = $("#addjobtitletable tr").length;
            $('<tr id="addjobtitle_' + rowlength + '">').appendTo("#addjobtitletable");
            $('<td><div class="checkbox"><input type="checkbox" id="jbactive_' + rowlength + '" class="jobtitlecheckbox css-checkbox" checked="" name="jobtitlecheckbox"><label class="css-label" for="jbactive_' + rowlength + '"></label></div><input type="hidden" value="active" name="data[Org][jobtitleactive][]" class="jobtitleactive"><input type="hidden" value="unsave" name="data[Org][jobtitlesave][]" class="jobtitlesaveunsave"></td>').appendTo("#addjobtitle_" + rowlength);
            $('<td class="jobtitleselectrow"><div class = "select-style"><select name="data[Org][jobtitles][]" class="jobtitlevalues form-control valid"><option value="">Select Job Title</option></select></div><input type="hidden" name="data[Org][jobtitle_other_department][]" class="form-control other_department"></td>').appendTo("#addjobtitle_" + rowlength);
            $('<td><?php echo $this->Html->image("EditRow.png", array("class" => "editjobtitle")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savejobtitle")); ?></td>').appendTo("#addjobtitle_" + rowlength);
            $('<td><?php echo $this->Html->image("DeleteRow.png", array("class" => "deletejobtitle")); ?></td>').appendTo("#addjobtitle_" + rowlength);
            $('</tr>').appendTo("#addjobtitle_" + rowlength);
            for (tmpvalues in jvalues) {
                $('<option value="' + jvalues[tmpvalues] + '">' + jvalues[tmpvalues] + '</option>').appendTo("#addjobtitle_" + rowlength + " .jobtitlevalues");
            }
//            }
//            $("#addjobtitlediv").show();
        });

        //adding new entity dynamically
        $(document).on('click', '#addentity', function () {
            //var rowlength = $("#addentitytable tr:visible").length;
            if (typeof ($("#addentitytable tr:last").attr("id")) == "undefined") {
                $("#addentitydiv").show();
                var rowlength = 1;
            } else {
                var rowlength = parseInt($("#addentitytable tr:last").attr("id").split("_")[1]) + 1;
            }

            //if (rowlength <= 0 || $('#addentitydiv').is(":visible")) {
            var rowlength = $("#addentitytable tr").length;
            $('<tr id="addentity_' + rowlength + '">').appendTo('#addentitytable');
            $('<td><div class="checkbox"><input type="checkbox" id="entityid' + rowlength + '" class="entitycheckbox css-checkbox" checked="" name="entirycheckbox"><label class="css-label" for="entityid' + rowlength + '"></label></div><input type="hidden" class="entityactive" name="data[Org][entityactive][]" value="active"><input type="hidden" class="entitysaveunsave" name="data[Org][entitysave][]" value="unsave"></td>').appendTo('#addentity_' + rowlength + '');
            $('<td class="entityvaluestextbox"><input placeholder = "Add Sub Organization" type="textbox" name="data[Org][entitytextbox][]" class="entitytextbox"></td>').appendTo('#addentity_' + rowlength + '');
            $('<td><?php echo $this->Html->image("EditRow.png", array("class" => "editentity")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "saveentity")); ?></td>').appendTo('#addentity_' + rowlength + '');
            $('<td><?php echo $this->Html->image("DeleteRow.png", array("class" => "deleteentity")); ?></td>').appendTo('#addentity_' + rowlength + '');
            $('</tr>').appendTo('#addentity_' + rowlength + '');
//            }
//            $("#addentitydiv").show();
        });
    });
</script>
<?php echo $this->Html->script('customeditorg'); ?>
<?php
$data = array(
    "textcenter" => "Edit Organization",
    "righttabs" => "1"
);
//echo $authUser["role"]; exit;
$headerpage = ($authUser["role"] == 1) ? 'header' : 'headerorg';
if ($authUser["role"] == 2) {
    $data['auth_users'] = $authUser;
}
echo $this->Element($headerpage, array('data' => $data));
?>

<section> 
    <?php echo $this->Form->create('Orgphoto', array('url' => array('controller' => 'users', 'action' => 'setorgimage'))); ?>
    <input type="hidden" name="orgid" id="organization_id"  value="<?php echo $org_id; ?>" />
    <div class="row createEditOrg">
        <div class="col-lg-12 ">
            <?php
            if ($org_image == "") {
                echo $this->Html->image('comp_pic.png', array('width' => '214', 'id' => 'org_image'));
            } else {
                $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR . $this->request->data['Org']['image'];
                echo $this->Html->image($org_imagenew, array('width' => '175', 'id' => 'org_image'));
            }
            ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <button type="button"  id="org_upload_photo" class="btn btn-blue">Upload Picture</button>
            &nbsp;&nbsp;
            <button type="button" id="org_remove_photo" class="btn btn-blue">Remove Picture</button>
        </div>
    </div>
    <?php
    echo $this->Form->input('Userphoto', array(
        'type' => 'file',
        'id' => 'photo',
        'label' => false,
        'class' => 'btn_uplaod_file hidden'
    ));
    echo $this->Form->end();
    ?>
</section>

<!--Used to display validation errors-->
<?php if ($errormsg != '') { ?>
    <div class="error-createclient"><?php echo $errormsg; ?></div>
<?php } ?>
<!--ends here--> 
<?php echo $this->Form->create('Org'); ?> <?php echo $this->Form->input('image', array('class' => 'form-control', 'label' => false, 'type' => 'hidden', 'id' => 'org_image_name', 'value' => $org_image)); ?>
<section>
    <div class="row">
        <section>
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-3">
                        <div class="labelCus">ID</div>
                    </div>
                    <div class="col-md-9">
                        <input type="text" id="inputEmail" value="<?php echo $org_id; ?>" class="form-control" readonly="readonly" >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-3">
                        <div class="labelCus">Short Name</div>
                    </div>
                    <div class="col-md-9"> <?php echo $this->Form->input('short_name', array('placeholder' => 'Short Name', 'class' => 'form-control', 'label' => false)); ?> </div>
                </div>
            </div>
        </section>
        <section>
            <div class="row">
                <div class="col-md-6"> 
                    <!--<div class="col-md-1"></div> -->
                    <div class="col-md-3">
                        <div class="labelCus require">Name</div>
                    </div>
                    <div class="col-md-9"> <?php echo $this->Form->input('name', array('placeholder' => 'Name', 'class' => 'form-control', 'label' => false)); ?> </div>
                </div>
                <div class="col-md-6"> 
                    <!--<div class="col-md-1"></div> -->
                    <div class="col-md-3">
                        <div class="labelCus">Industry</div>
                    </div>
                    <div class="col-md-9">
                        <div class="select-style"> <?php echo $this->Form->input('industry', array('empty' => 'select industry', 'label' => false, 'options' => $industry, 'selected' => $industry_value, 'class' => 'form-control')); ?> </div>
                    </div>
                </div>
        </section>
        <section>
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-3">
                        <div class="labelCus" >Active</div>
                    </div>
                    <div class="col-md-9"> <span class="radio">
                            <?php
                            $options = array(
                                '1' => 'Yes',
                                '0' => 'No'
                            );
                            echo $this->Form->input('status', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'value' => $this->request->data['Org']['status']
                                    )
                            );
                            ?>
                        </span> </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-3">
                        <div class="labelCus" >Allow Attachments</div>
                    </div>
                    <div class="col-md-9"> <span class="radio">
                            <?php
                            $options = array(
                                '1' => 'Yes',
                                '0' => 'No'
                            );
                            echo $this->Form->input('allow_attachment', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'value' => $this->request->data['Org']['allow_attachment']
                                    )
                            );
                            ?>
                        </span> </div>
                </div>

            </div>
        </section>
        <section>
            <div class="row">
                <div class="col-md-6"> 
                    <!--<div class="col-md-1"></div> -->
                    <div class="col-md-3">
                        <div class="labelCus">Allow Comments</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio">
                            <?php
                            $options = array(
                                '1' => 'Yes',
                                '0' => 'No'
                            );
                            echo $this->Form->input('allow_comments', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'value' => $this->request->data['Org']['allow_comments'],
                                'class' => 'allowIt',
                                    )
                            );
                            ?>
                        </span> 
                    </div>
                </div>
                <!-- added by babulal prasad @28-02-2018 to show/hide leader board -->
                <div class="col-md-6"> 
                    <!--<div class="col-md-1"></div>--> 
                    <div class="col-md-3">
                        <div class="labelCus" >Leader Board Visible</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio">
                            <?php
                            $options = array(
                                '1' => 'Yes',
                                '0' => 'No'
                            );
                            echo $this->Form->input('show_leader_board', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'value' => $this->request->data['Org']['show_leader_board']
                                    )
                            );
                            ?>
                        </span> 
                    </div>
                </div>

            </div>
        </section>
        <section>
            <div class="row">
                <div class="col-md-6 publicEndorse"> 
                    <!--<div class="col-md-1"></div> -->
                    <div class="col-md-3">
                        <div class="labelCus">Public nDorsement Comment Visible on Live Feed</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio">
                            <?php
                            $options = array(
                                '1' => 'Yes',
                                '0' => 'No'
                            );
                            echo $this->Form->input('public_endorse_visible', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'value' => $this->request->data['Org']['public_endorse_visible'],
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
                <div class="col-md-6  commentcompulsory"> 
                    <!--<div class="col-md-1"></div> -->
                    <div class="col-md-3">
                        <div class="labelCus" >Comment Optional</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio">
                            <?php
                            $options = array(
                                '1' => 'Yes',
                                '0' => 'No'
                            );
                            echo $this->Form->input('optional_comments', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'class' => 'optional_comments',
                                'value' => $this->request->data['Org']['optional_comments']
                                    )
                            );
                            ?>
                        </span> 
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div class="row">
                <div class="col-md-6"> 
                    <!--<div class="col-md-1"></div> -->
                    <div class="col-md-3">
                        <div class="labelCus">Enable Customer Portal</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio">
                            <?php
                            $options = array(
                                '1' => 'Yes',
                                '0' => 'No'
                            );
                            echo $this->Form->input('allow_customer_portal', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'value' => $this->request->data['Org']['allow_customer_portal'],
                                'class' => 'endorse-visible-alert',
                                    )
                            );
                            ?>
                        </span> 
                    </div>
                </div>
                <?php $classMsgLimit = $this->request->data['Org']['optional_comments'] ? "hide" : ""; ?>
                <div class="col-md-6 <?php echo $classMsgLimit; ?>" id="minimum_characters_div">
                    <div class="col-md-3">
                        <div class="labelCus">Minimum characters for Message Box</div>
                    </div>
                    <div class="col-md-9"> <?php echo $this->Form->input('endorse_message_min_limit', array('placeholder' => 'Minimum characters for Message Box', 'class' => 'form-control ', 'label' => false)); ?> </div>
                </div>

            </div>
        </section>

        <?php if ($authUser["role"] == 1) { ?> 
            <section>
                <div class="row">
                    <div class="col-md-6"> 
                        <!--<div class="col-md-1"></div> -->
                        <div class="col-md-3">
                            <div class="labelCus">Enable Video Feature</div>
                        </div>
                        <div class="col-md-9"> 
                            <span class="radio">
                                <?php
                                $options = array(
                                    '1' => 'Yes',
                                    '0' => 'No'
                                );
                                echo $this->Form->input('featured_video_enabled', array('type' => 'radio',
                                    'separator' => '</div><div>',
                                    'before' => '<div class="col-md-3">',
                                    'after' => '</div>',
                                    'options' => $options,
                                    'label' => true,
                                    'legend' => false,
                                    'value' => $this->request->data['Org']['featured_video_enabled'],
                                    'class' => '',
                                        )
                                );
                                ?>
                            </span> 
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-3">
                            <div class="labelCus">Maximum Limit Of One To One nDorsements For Month:</div>
                        </div>
                        <div class="col-md-9"> <?php echo $this->Form->input('endorsement_limit', array('placeholder' => 'Maximum Limit Of One To One nDorsements For Month', 'class' => 'form-control', 'label' => false)); ?> </div>
                    </div>

                </div>
            </section>
        <?php } ?>
        <section>
            <div class="row">
                <div class="col-md-6"> 
                    <!--<div class="col-md-1"></div> -->
                    <div class="col-md-3">
                        <div class="labelCus">Enable DAISY Portal</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio">
                            <?php
                            $options = array(
                                '1' => 'Yes',
                                '0' => 'No'
                            );
                            echo $this->Form->input('enable_daisy_portal', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'value' => $this->request->data['Org']['enable_daisy_portal'],
                                'class' => '',
                                    )
                            );
                            ?>
                        </span> 
                    </div>
                </div>
                <div class="col-md-6"> 
                    <!--<div class="col-md-1"></div> -->
                    <div class="col-md-3">
                        <div class="labelCus">Only Admin can POST</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio">
                            <?php
                            $options = array(
                                '1' => 'Yes',
                                '0' => 'No'
                            );
                            echo $this->Form->input('only_admin_post', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'value' => $this->request->data['Org']['only_admin_post'],
                                'class' => '',
                                    )
                            );
                            ?>
                        </span> 
                    </div>
                </div>
                <div class="col-md-6"> 
                    <!--<div class="col-md-1"></div> -->
                    <div class="col-md-3">
                        <div class="labelCus">Enable Customize Stickers</div>
                    </div>
                    <div class="col-md-9"> 
                        <span class="radio">
                            <?php
                            $options = array(
                                '1' => 'Yes',
                                '0' => 'No'
                            );
                            echo $this->Form->input('personalized_bitmoji_enabled', array('type' => 'radio',
                                'separator' => '</div><div>',
                                'before' => '<div class="col-md-3">',
                                'after' => '</div>',
                                'options' => $options,
                                'label' => true,
                                'legend' => false,
                                'value' => $this->request->data['Org']['personalized_bitmoji_enabled'],
                                'class' => '',
                                    )
                            );
                            ?>
                        </span> 
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
                    <div class="col-md-9 ">
                        <div class="select-style"> <?php echo $this->Form->input('country', array('empty' => 'Select Country', 'selected' => $country_id, 'class' => 'form-control country', 'label' => false, 'options' => $listCountries, 'data-url' => Router::url(array('controller' => 'ajax', 'action' => 'states')))); ?> </div>
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
                            if (empty($listState)) {
                                $stylestatelist = "style=display:none;";
                                $stylestatetextlist = "style=display:block;";
                            }
                            $selectedState = "'" . $this->request->data['Org']['state'] . "'";
                            ?>
                            <div class="select-style" id="selectstate" <?php echo $stylestatelist; ?> > <?php echo $this->Form->input('state', array('empty' => 'Select State', 'default' => $selectedState, 'label' => false, 'options' => $listState, 'class' => 'form-control states')); ?> </div>
                            <div id="selectstatetext" <?php echo $stylestatetextlist; ?> >
                                <?php echo $this->Form->input('state_name', array('type' => 'text', 'class' => 'textbox', 'id' => 'state_name', 'label' => false)); ?>
                            </div>
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
        <!--ends here-->
        <section>
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-3">
                        <div class="labelCus">About</div>
                    </div>
                    <div class="col-md-9"> <?php echo $this->Form->input('about', array('placeholder' => 'About', 'class' => 'form-control', 'label' => false, 'type' => 'textarea')); ?> </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-3">
                        <div class="labelCus require">Manager Report Code</div>
                    </div>
                    <div class="col-md-9"> 
                        <?php echo $this->Form->input('manager_code', array('placeholder' => 'Manager Report Code', 'class' => 'form-control txt-decimal', 'label' => false, 'type' => 'text', "onkeypress" => "return isNumberKey(event);")); ?> </div>
                </div>
            </div>
        </section>
    </div>
</section>



<?php
//   Hide for 6.4 version. Unhide for 6.5.1
//pr($org_data['Organization']['org_bitmojis']);
?>
<section class="AddCoreValue">
    <div class="row AddCoreValue">
        <div class="col-md-12">
            <div class="pull-left">
                <h4 class="require">ADD STICKERS</h4>
            </div>
            <div class="pull-right"><span>Add New Sticker</span> 
                <a href="#" data-toggle="modal" data-target="#myModal_addstickers" class="pending-invites">
                    <?php echo $this->Html->image('addCoreValue.png'); ?></a></div>

        </div>
    </div>
    <div class="row addSticker">
        <div class="col-md-12 sticBg" style="height: 350px;">
            <div class="sticker-content" style="height: 90%;">
                <!--#sticker-container-edit-->
                <h3>Org Stickers (Click Image to Select/Deselect)</h3>
                <div class="sticker-container-edit">
                    <?php
//                    pr($emojis);
//                    exit;
                    foreach ($emojis as $index => $sticker) {
                        if ($sticker->personalized == 0) {
                            continue;
                        }
                        if ($sticker->title == "") {
                            $stickerName = str_replace(".gif", "", $sticker->image);
                            $stickerName = str_replace("-100", "", $stickerName);
                            $stickerName = str_replace("_100", " ", $stickerName);
                            $stickerName = str_replace("_", " ", $stickerName);
                        } else {
                            $stickerName = $sticker->title;
                        }
                        $bitmojiID = $sticker->id;
//                        pr($orgBitmojiArray); exit;
                        if (($bitmojiID != '') && (in_array($bitmojiID, $orgBitmojiArray))) {
                            ?>
                            <div class="sticker-img js_addSticker js_stickerAdded" data-id="<?php echo $bitmojiID; ?>" id="custom_sticker_<?php echo $bitmojiID; ?>">
                                <div class="stikrDel delete_sticker" data-id="<?php echo $bitmojiID; ?>"><a href="javascript:void(0);"></a></div>
                                <div class="relPosi">
                                    <?php echo $this->Html->Image($sticker->url, array("class" => "attached-item",)); ?>
                                    <div class="sticker-title"><?php echo $stickerName; ?></div>
                                </div>
                                <div class="switchbutton">
                                    <?php echo $this->Html->Image("selected-org.png", array("class" => "defaultorg",)); ?>
                                </div>

                            </div>
                        <?php } else { ?>
                            <div class="sticker-img js_addSticker" data-id="<?php echo $bitmojiID; ?>" id="custom_sticker_<?php echo $bitmojiID; ?>">
                                <div class="stikrDel delete_sticker" data-id="<?php echo $bitmojiID; ?>"><a href="javascript:void(0);"></a></div>
                                <div class="relPosi">
                                    <?php echo $this->Html->Image($sticker->url, array("class" => "attached-item",)); ?>
                                    <div class="sticker-title"><?php echo $stickerName; ?></div>
                                </div>


                            </div>
                            <?php
                        }
                    }
                    ?>

                </div>   
                <!--#sticker-container-edit--> 
                <hr/>
                <h3>Default Stickers (Click Image to Select/Deselect)</h3>
                <div class="sticker-container">
                    <?php echo $this->Form->input('OrgSelectedStickers', array('id' => 'OrgSelectedStickers', 'type' => 'hidden', 'value' => '')); ?> 
                    <div id="stickerPanel" >

                    </div>

                    <!--#--->
                    <?php
//                pr($emojis); //exit; 
                    foreach ($emojis as $index => $sticker) {
                        if ($sticker->personalized == 1) {
                            continue;
                        }
                        if ($sticker->title == "") {
                            $stickerName = str_replace(".gif", "", $sticker->image);
                            $stickerName = str_replace("-100", "", $stickerName);
                            $stickerName = str_replace("_100", " ", $stickerName);
                            $stickerName = str_replace("_", " ", $stickerName);
                        } else {
                            $stickerName = $sticker->title;
                        }

                        $bitmojiID = $sticker->id;
                        if (($bitmojiID != '') && (in_array($bitmojiID, $orgBitmojiArray))) {
                            ?>
                            <div class="sticker-img js_addSticker js_stickerAdded" data-id="<?php echo $bitmojiID; ?>">
                                <div class="relPosi">
                                    <?php echo $this->Html->Image($sticker->url, array("class" => "attached-item",)); ?>
                                    <div class="sticker-title"><?php echo $stickerName; ?></div>
                                </div>
                                <div class="switchbutton">
                                    <?php echo $this->Html->Image("selected-org.png", array("class" => "defaultorg",)); ?>
                                </div>

                            </div>
                        <?php } else { ?>
                            <div class="sticker-img js_addSticker" data-id="<?php echo $bitmojiID; ?>">
                                <div class="relPosi">
                                    <?php echo $this->Html->Image($sticker->url, array("class" => "attached-item",)); ?>
                                    <div class="sticker-title"><?php echo $stickerName; ?></div>
                                </div>


                            </div>
                        <?php } ?>

                        <!--#--->
                    <?php }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!--</div>-->
</section>



<!-- Sticker Modal box-->
<div class="modal fade" id="myModal_addstickers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" align="left">
            <div class="modal-header">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="upStikcer form-control">
                        <input id="uploadSticker" type="file" name="new_sticker" class="uploads"  accept=".gif" />
                        <label for="uploadSticker">upload Sticker</label>
                    </div>
                </div>

                <div class="form-group">
                    Sticker Title : <input class="form-control" id="title_add" type = "text" name = "sticker_title" value = ""/>
                    <p class="title_addErr err" style="color: red; margin-bottom: 0%; margin-left: 1%;display: none;"><i>Please enter title.</i></p>
                </div>
                <div class="form-group bitmojiFormGrp">
                    <button type="button" id="save_stickers" class="btn btn-primary btn-blue">Add Sticker</button>
                    <div class="bitmojiloader" id="export-loader-img" style="display: none;margin-top: -7%;margin-left: 21%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>


<section class="AddCoreValue">
    <div class="row AddCoreValue">
        <div class="col-md-12">
            <div class="pull-left">
                <h4 class="require">ADD SUB CENTER</h4>
            </div>
            <div class="pull-right"><span>Add a New Sub Center</span> <a href="javascript:void(0);">
                    <?php echo $this->Html->image('addCoreValue.png', array('id' => 'addsubcenter')); ?></a></div>
        </div>
    </div>
    <?php $subCenterCount = count($orgSubcenterDetail); ?>

    <div class="row CreateEditOrg" id="subcentertable" style="" >
        <table id="addsubcentertable" class="table table-hover cvTable" >
            <tr id="addcenterrow_0">
                <th style="width: 35%;">Sub Center Long Name</th>
                <th style="width: 20%;">Short Name</th>
                <th width="20%">Edit / Save</th>
                <th width="20%">Delete</th> 
            </tr>
            <?php
            $SCenterCounter = 1;
            if ($subCenterCount > 0) {
                foreach ($orgSubcenterDetail as $index => $subcenter) {
                    $subCdata = $subcenter['OrgSubcenter'];
                    ?>
                    <tr id = "addcenterrow_<?php echo $SCenterCounter; ?>">
                        <td class = "custm-msg">
                            <input type="hidden" value="<?php echo $subCdata['id']; ?>" name="data[Org][subcenter_id][]">
                            <input class="subcenterlongvalue form-control" type = "text" name = "data[Org][subcenter_long][]" value = "<?php echo $subCdata['long_name']; ?>" style="display:none;" />
                            <p class="sub_longname"><?php echo $subCdata['long_name']; ?></p>
                            <input type="hidden" id="SCactive" name="data[Org][SCactive][]" value="<?php echo ($subCdata['status']) ? 'active' : 'inactive'; ?>">
                            <input type="hidden" id="SCsaveunsave" name="data[Org][SCsave][]" value="save">
                            <input type="hidden" id="SChiddenid" name="data[Org][SChiddenid][]" value="<?php echo $subCdata['id']; ?>">
                        </td>
                        <td class = "custm-msg">
                            <input class="subcentershortvalue form-control" type = "text" name = "data[Org][subcenter_short][]" value = "<?php echo $subCdata['short_name']; ?>" style="display:none;"/>
                            <p class="sub_shortname"><?php echo $subCdata['short_name']; ?></p>
                        </td>
                        <td width = "20%"><?php echo $this->Html->image("EditRow.png", array("id" => "editsubcenter")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savesubcenter")); ?></td>
                        <td width="20%"><?php echo $this->Html->image("DeleteRow.png", array("id" => "deletesubcenter")); ?></td>
                    </tr>
                    <?php
                    $SCenterCounter++;
                }
            } else {
                ?>
                <tr id = "addcenterrow_1">
                    <td class = "custm-msg">
                        <input type="hidden" value="" name="data[Org][subcenter_id][]">
                        <input class="subcenterlongvalue form-control" type = "text" name = "data[Org][subcenter_long][]" value = "" style="display:block;" />
                        <p class="sub_longname" style="display:none;"></p>
                        <input type="hidden" id="SCactive" name="data[Org][SCactive][]" value="active">
                        <input type="hidden" id="SCsaveunsave" name="data[Org][SCsave][]" value="unsave">
                        <input type="hidden" id="SChiddenid" name="data[Org][SChiddenid][]" value="">
                    </td>
                    <td class = "custm-msg">
                        <input class="subcentershortvalue form-control" type = "text" name = "data[Org][subcenter_short][]" value = "" style="display:block;"/>
                        <p class="sub_shortname" style="display:none;"></p>
                    </td>
                    <td width = "20%"><?php echo $this->Html->image("EditRow.png", array("id" => "editsubcenter")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savesubcenter")); ?></td>
                    <td width="20%"><?php echo $this->Html->image("DeleteRow.png", array("id" => "deletesubcenter")); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</section>

<section class="AddCoreValue">
    <div class="row AddCoreValue">
        <div class="col-md-12">
            <div class="pull-left">
                <h4 class="require">ADD HASHTAG</h4>
            </div>
            <div class="pull-right"><span>Add a New Hashtag</span> <a href="javascript:void(0);">
                    <?php echo $this->Html->image('addCoreValue.png', array('id' => 'addhashtag')); ?></a></div>
        </div>
    </div>
    <?php $hashTagCount = count($orgHashTagDetail); ?>

    <div class="row CreateEditOrg" id="hashtagtable" style="" >
        <table id="addhashtagtable" class="table table-hover cvTable" >
            <tr id="addhashtagrow_0">
                <th width="50%">HashTag Name</th>
                <th width="30%" style="width: 30% !important;">Edit / Save</th>
                <th width="20%">Delete</th> 
            </tr>
            <?php
            $hashCounter = 1;

            if ($hashTagCount > 0) {
                foreach ($orgHashTagDetail as $index => $hashtags) {
                    $hashtag = $hashtags['OrgHashtag'];
                    ?>
                    <tr id = "addhashtagrow_<?php echo $hashCounter; ?>">
                        <td class = "custm-msg">
                            <input type="hidden" value="<?php echo $hashtag['id']; ?>" name="data[Org][hashtag_id][]">

                            <input class="hashtagvalues form-control" type = "text" name = "data[Org][hashtag][]" value = "<?php echo $hashtag['name']; ?>" style="display: none" />
                            <p class="corevalue"><?php echo $hashtag['name']; ?></p>

                            <input type="hidden" id="HTactive" name="data[Org][HTactive][]" value="<?php echo ($hashtag['status']) ? 'active' : 'inactive'; ?>">
                            <input type="hidden" id="HTsaveunsave" name="data[Org][HTsave][]" value="save">
                            <input type="hidden" id="HThiddenid" name="data[Org][HThiddenid][]" value="<?php echo $hashtag['id']; ?>">
                        </td>
                        <td width = "30%"><?php echo $this->Html->image("EditRow.png", array("id" => "edithashtag")); ?> / 
                            <?php echo $this->Html->image("SaveRow.png", array("class" => "savehashtag")); ?></td>
                        <td width="20%"><?php echo $this->Html->image("DeleteRow.png", array("id" => "deletehashtag")); ?></td>

                    </tr>
                    <?php
                    $hashCounter++;
                }
            } else {
                ?>
                <tr id = "addhashtagrow_1">
                    <td class = "custm-msg">
                        <input type="hidden" value="" name="data[Org][hashtag_id][]">
                        <input class="hashtagvalues form-control" type = "text" name = "data[Org][hashtag][]" value = "" style="display: block;" />
                        <p class="corevalue" style="display: none;" ></p>
                        <input type="hidden" id="HTactive" name="data[Org][HTactive][]" value="active">
                        <input type="hidden" id="HTsaveunsave" name="data[Org][HTsave][]" value="save">
                        <input type="hidden" id="HThiddenid" name="data[Org][HThiddenid][]" value="">
                    </td>
                    <td width = "30%"><?php echo $this->Html->image("EditRow.png", array("id" => "edithashtag")); ?> / 
                        <?php echo $this->Html->image("SaveRow.png", array("class" => "savehashtag")); ?></td>
                    <td width="20%"><?php echo $this->Html->image("DeleteRow.png", array("id" => "deletehashtag")); ?></td>
                </tr>
            <?php }
            ?>
        </table>
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
    <?php
    $counter_core_values = (isset($existing_corevalues)) ? count($existing_corevalues) : "";
    $display = "display:none";
    if ($counter_core_values) {
        $display = "display:block";
    }
    ?>
    <div class="row CreateEditOrg" id="corevaluetable" style="<?php echo $display; ?>" >
        <table id="addcoretable" class="table table-hover cvTable" >
            <tr id="addcorerow_0">
                <th >For DAISY</th>
                <th >For Webapp</th>
                <th >For Feedback</th>
                <th >Active</th>
                <th >Core value</th>
                <th >Color Code</th>
                <th >Edit / Save</th>
                <th >Custom Message</th>
                <th >Delete</th>
            </tr>
            <?php
            if (!empty($resultantdepartment["corevalues"])) {
                for ($i = 0; $i < count($resultantdepartment["corevalues"]["value"]); $i++) {
                    $dept = $this->request->data['Org']['corevalues'];
                    ?>
                    <tr id="addcorerow_<?php echo $i + 1; ?>">
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="cvforweb_<?php echo $i + 1; ?>" class="css-checkbox" value="1" checked="checked" name="data[Org][cvfordaisy][]">
                                <label for="cvfordaisy_<?php echo $i + 1; ?>" class="css-label"></label>
                            </div>
                        </td>
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="cvforweb_<?php echo $i + 1; ?>" class="css-checkbox" value="1" checked="checked" name="data[Org][cvforweb][]">
                                <label for="cvforweb_<?php echo $i + 1; ?>" class="css-label"></label>
                            </div>
                        </td>
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" id="cvforguest_<?php echo $i + 1; ?>" class="css-checkbox" value="1" checked="checked" name="data[Org][cvforguest][<?php echo $i; ?>]">
                                <label for="cvforguest_<?php echo $i + 1; ?>" class="css-label"></label>
                            </div>
                        </td>
                        <td><?php //echo $this->Form->checkbox('cvactivestatus', array('hiddenField' => false, ($existing_corevalues[$i]['org_core_values']['status']) ? "checked" : "" , "name"=> "data[Org][cvactivestatus][]"));                                                                                                                                                                    ?>
                            <div class="checkbox">
                                <input type="checkbox" id="cvid_<?php echo $i + 1; ?>" class="OrgCvactivestatus css-checkbox" value="1" checked="checked" name="data[Org][cvactivestatus][<?php echo $i; ?>]">
                                <label for="cvid_<?php echo $i + 1; ?>" class="css-label"></label>
                            </div>
                            <?php
                            if (!empty($resultantdepartment["corevalues"]["hiddenid"][$i])) {
                                echo '<input type="hidden" value="' . $resultantdepartment["corevalues"]["hiddenid"][$i] . '" name="data[Org][hiddenid][]" class="departmetdbid">';
                            }
                            ?>
                            <input type="hidden" id="cvactive" name="data[Org][cvactive][]" value="<?php echo $resultantdepartment["corevalues"]["activestatus"][$i]; ?>">
                            <input type="hidden" id="saveunsave" class="cvsaveunsave" name="data[Org][save][]" value="<?php echo $resultantdepartment["corevalues"]["savestatus"][$i]; ?>">
                        </td>
                        <td id="corevaluesdropdown"><?php $style = ($resultantdepartment["corevalues"]["savestatus"][$i] == "save") ? "none" : "block"; ?>
                            <div class = "select-style">
                                <select class="form-control" id="OrgCorevalues" name="data[Org][corevalues][]" id="" style="display: <?php echo $style; ?>">
                                    <option value="">Select Corevalues</option>
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
                            ?>
                        </td>
                        <td ><?php
                            $colorvalue = $resultantdepartment["corevalues"]["colorcode"][$i];
                            ?>
                            <input type="hidden" name="data[Org][cp][]" value="<?php echo $colorvalue; ?>"><input type="text" style="color: <?php echo $colorvalue; ?>; background-color: #001e52"  value="Hello" id="colorpick"/>
                        </td>
                        <td id="savecv"><?php echo $this->Html->image('EditRow.png', array('id' => 'editcorevalues')); ?> / <?php echo $this->Html->image('SaveRow.png', array('class' => 'savecorevalues')); ?></td>
                        <td class="custm-msg">
                            <div class="checkbox">
                                <input type="checkbox" id="customeditid_<?php echo $i; ?>" class="customCoreMessage css-checkbox" value="1" checked="checked" name="data[Org][custom_message_enabled1][<?php echo $i; ?>]">
                                <label for="customeditid_<?php echo $i; ?>" class="css-label"></label>
                                <?php //echo $this->Html->image('EditRow.png', array('id' => 'editcustommsg', 'class' => 'custommsgattr'));           ?> 
                                <?php //echo $this->Html->image('SaveRow.png', array('class' => 'savecustommsg'));                ?>
                            </div>
                            <textarea class="custommsgattrtextarea" name="data[Org][custom_message_text][<?php echo $i; ?>]"> </textarea>
                        </td>
                        <td id="deletecv"><?php echo $this->Html->image('DeleteRow.png', array('id' => 'deletecorevalues')); ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <?php
                if (isset($existing_corevalues)) {
//                    pr($existing_corevalues); exit;
                    ?>
                    <?php
                    for ($i = 0; $i < $counter_core_values; $i++) {
                        $others_department = "";
                        ?>
                        <tr id="addcorerow_<?php echo $i + 1; ?>">
                            <td>
                                <?php $checkedDaisy = $existing_corevalues[$i]['org_core_values']['for_daisy'] ? "checked" : ""; ?>
                                <div class="checkbox">
                                    <input type="checkbox" id="cvfordaisy_<?php echo $i + 1; ?>" class="css-checkbox" <?php echo $checkedDaisy; ?> value="1" name="data[Org][cvfordaisy][<?php echo $i; ?>]">
                                    <label for="cvfordaisy_<?php echo $i + 1; ?>" class="css-label"></label>
                                </div>
                            </td>
                            <td>
                                <?php $checkedWebapp = $existing_corevalues[$i]['org_core_values']['for_webapp'] ? "checked" : ""; ?>
                                <div class="checkbox">
                                    <input type="checkbox" id="cvforweb_<?php echo $i + 1; ?>" class="css-checkbox" <?php echo $checkedWebapp; ?> value="1" name="data[Org][cvforweb][<?php echo $i; ?>]">
                                    <label for="cvforweb_<?php echo $i + 1; ?>" class="css-label"></label>
                                </div>
                            </td>
                            <td>
                                <?php $checkedGuest = $existing_corevalues[$i]['org_core_values']['for_guest'] ? "checked" : ""; ?>
                                <div class="checkbox">
                                    <input type="checkbox" id="cvforguest_<?php echo $i + 1; ?>" class="css-checkbox" <?php echo $checkedGuest; ?> value="1" name="data[Org][cvforguest][<?php echo $i; ?>]">
                                    <label for="cvforguest_<?php echo $i + 1; ?>" class="css-label"></label>
                                </div>
                            </td>
                            <td><?php
                                //echo $this->Form->checkbox('cvactivestatus', array('hiddenField' => false, ($existing_corevalues[$i]['org_core_values']['status']) ? "checked" : "" , "name"=> "data[Org][cvactivestatus][]")); 
                                $checked = $existing_corevalues[$i]['org_core_values']['status'] ? "checked" : "";
                                ?>
                                <div class="checkbox">
                                    <input type="checkbox" id="cvid_<?php echo $i + 1; ?>" class="OrgCvactivestatus css-checkbox" value="1" <?php echo $checked; ?> name="data[Org][cvactivestatus][]">
                                    <label for="cvid_<?php echo $i + 1; ?>" class="css-label"></label>
                                </div>
                                <input type="hidden" id="cvactive" name="data[Org][cvactive][]" value="<?php echo ($existing_corevalues[$i]['org_core_values']['status']) ? 'active' : 'inactive'; ?>">
                                <input type="hidden" id="saveunsave" name="data[Org][save][]" value="save">
                                <input type="hidden" id="hiddenid" name="data[Org][hiddenid][]" value="<?php echo $existing_corevalues[$i]['org_core_values']['id']; ?>">
                            </td>
                            <?php
                            if (!in_array($existing_corevalues[$i]['org_core_values']['name'], $corevalues)) {
                                $others_department = $existing_corevalues[$i]['org_core_values']['name'];
                            }
                            ?>
                            <td id="corevaluesdropdown"><div class = "select-style"> <?php echo $this->Form->input('corevalues', array('empty' => 'Select Core Value', 'label' => false, 'options' => $corevalues, 'selected' => $existing_corevalues[$i]['org_core_values']['name'], 'class' => 'form-control', 'style' => 'display:none', 'name' => 'data[Org][corevalues][]')); ?> </div>
                                <div id="othercv" style="display:none"><?php echo $this->Form->input('other_department', array('class' => 'form-control', 'id' => 'OrgOtherDepartment', 'label' => false, 'name' => 'data[Org][other_department][]', 'value' => $others_department)); ?></div>
                                <p id='corevalue'><?php echo $existing_corevalues[$i]['org_core_values']['name']; ?></p>
                            </td>
                            <td ><input type="hidden" name="data[Org][cp][]" value="<?php echo $existing_corevalues[$i]['org_core_values']['color_code']; ?>">
                                <input style="background-color: #001e52 ;color: <?php echo $existing_corevalues[$i]['org_core_values']['color_code']; ?>" id="colorpick" readonly="readonly" type="text" value="Hello"/>
                            </td>
                            <td id="savecv"><?php echo $this->Html->image('EditRow.png', array('id' => 'editcorevalues')); ?> <?php echo $this->Html->image('SaveRow.png', array('class' => 'savecorevalues')); ?></td>
                            <td class="custm-msg">
                                <?php $checkedEnabled = $existing_corevalues[$i]['org_core_values']['custom_message_enabled'] ? "checked" : ""; ?>
                                <div class="checkbox">
                                    <input type="checkbox" id="customeditid_<?php echo $i; ?>" class="customCoreMessage css-checkbox" value="1" <?php echo $checkedEnabled; ?> name="data[Org][custom_message_enabled][<?php echo $i; ?>]">
                                    <label for="customeditid_<?php echo $i; ?>" class="css-label"></label>
                                    <?php // echo $this->Html->image('EditRow.png', array('id' => 'editcustommsg', 'class' => 'custommsgattr'));          ?> 
                                    <?php //echo $this->Html->image('SaveRow.png', array('class' => 'savecustommsg custommsgattr'));           ?>
                                </div>
                                <?php
                                $checkedEnabledTextClass = $existing_corevalues[$i]['org_core_values']['custom_message_enabled'] ? "" : "hide";
                                ?>
                                <textarea class="custommsgattrtextarea <?php echo $checkedEnabledTextClass; ?>" name="data[Org][custom_message_text][<?php echo $i; ?>]"> <?php echo $existing_corevalues[$i]['org_core_values']['custom_message_text']; ?></textarea>

                            </td>
                            <td id="deletecv"><?php echo $this->Html->image('DeleteRow.png', array('id' => 'deletecorevalues')); ?></td>
                        </tr>
                        <?php
                    }
                }
            }
            ?>
        </table>
    </div>
</section>


<!-- Edit Entity-->
<section class="AddCoreValue">
    <div class="row AddCoreValue">
        <div class="col-md-12">
            <div class="pull-left">
                <h4>ENTER SUB ORGANIZATION</h4>
            </div>
            <div class="pull-right"><span>Add a New Sub Organization</span> <a href="javascript:void(0);"><?php echo $this->Html->image('addCoreValue.png', array('id' => 'addentity')); ?></a></div>
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
    <div class="row CreateEditOrg" id="addentitydiv" style="<?php echo $display; ?>">
        <table class="table table-hover" id="addentitytable">
            <tr>
                <th width="15%">Active</th>
                <th width="45%">Sub Organization</th>
                <th width="20%">Edit / Save</th>
                <th width="20%">Delete</th>
            </tr>
            <?php
            if (!empty($resultantdepartment["entity"])) {
                for ($i = 0; $i < count($resultantdepartment["entity"]["value"]); $i++) {
                    ?>
                    <tr id="addentity_<?php echo $i + 1; ?>">
                        <td width="15%"><input type="checkbox" name="entirycheckbox" checked class="entitycheckbox" />
                            <input type="hidden" class="entityactive" name="data[Org][entityactive][]" value="<?php echo $resultantdepartment["entity"]["activestatus"][$i]; ?>">
                            <input type="hidden" class="entitysaveunsave" name="data[Org][entitysave][]" value="<?php echo $resultantdepartment["entity"]["savestatus"][$i]; ?>">
                        </td>
                        <?php $style = ($resultantdepartment["entity"]["savestatus"][$i] == "save") ? "none" : "block"; ?>
                        <td width="45%" class="entityvaluestextbox"><input placeholder="Add Sub Organization" value="<?php echo $resultantdepartment["entity"]["value"][$i]; ?>" class="entitytextbox" type="textbox" name="data[Org][entitytextbox][]" class="entitytextbox" style="display:<?php echo $style; ?>" />
                            <?php
                            if ($resultantdepartment["entity"]["savestatus"][$i] == "save") {
                                echo "<p class = 'corevalue'>" . $resultantdepartment["entity"]["value"][$i] . "</p>";
                            }
                            ?></td>
                        <td width="20%"><?php echo $this->Html->image("EditRow.png", array("class" => "editentity")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "saveentity")); ?></td>
                        <td width="20%"><?php echo $this->Html->image("DeleteRow.png", array("class" => "deleteentity")); ?></td>
                    </tr>
                    <?php
                }
            } else {
                $i = 1;
                if (isset($existing_entities)) {
                    foreach ($existing_entities as $id => $value) {
                        $others_department = "";
                        ?>
                        <tr id="addentity_<?php echo $i; ?>">
                            <td width="15%"><div class="checkbox">
                                    <input type="checkbox" id="entityid_<?php echo $i; ?>" name="entitycheckbox" <?php echo ($existing_entitiesstatus[$id]) ? "checked" : " "; ?> class="entitycheckbox css-checkbox" />
                                    <label for="entityid_<?php echo $i; ?>" class="css-label"></label>
                                </div>
                                <input type="hidden" class="entityid" name="data[Org][entityhiddenid][]" value="<?php echo $id; ?>">
                                <input type="hidden" class="entityactive" name="data[Org][entityactive][]" value="<?php echo ($existing_entitiesstatus[$id]) ? 'active' : 'inactive'; ?>">
                                <input type="hidden" class="entitysaveunsave" name="data[Org][entitysave][]" value="save">
                            </td>
                            <td width="45%" class="entityvaluestextbox"><p class = corevalue><?php echo $value; ?></p>
                                <input class="entitytextbox" type="hidden" value="<?php echo $value; ?>" name="data[Org][entitytextbox][]" class="entitytextbox" />
                            </td>
                            <td width="20%"><?php echo $this->Html->image("EditRow.png", array("class" => "editentity")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "saveentity")); ?></td>
                            <td width="20%"><?php echo $this->Html->image("DeleteRow.png", array("class" => "deleteentity")); ?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
            }
            ?>
        </table>
    </div>
</section>
<!--departments-->
<section class="AddCoreValue">
    <div class="row AddCoreValue">
        <div class="col-md-12">
            <div class="pull-left">
                <h4>ENTER DEPARTMENTS</h4>
            </div>
            <div class="pull-right"><span>Add a New Department</span> <a href="javascript:void(0);"><?php echo $this->Html->image('addCoreValue.png', array('id' => 'adddepartment')); ?></a></div>
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
    <div class="row CreateEditOrg" id="adddepartmentdiv" style="<?php echo $display; ?>">
        <table class="table table-hover" id="adddepartmenttable">
            <tr>
                <th width="15%">Active</th>
                <th width="45%">Department Name</th>
                <th width="20%">Edit / Save</th>
                <th width="20%">Delete</th>
            </tr>
            <?php
            if (!empty($resultantdepartment["department"])) {
                for ($i = 0; $i < count($resultantdepartment["department"]["value"]); $i++) {
                    $dept = $this->request->data['Org']['departments'];
                    ?>
                    <tr id="adddepartment_<?php echo $i + 1; ?>">
                        <td width="15%"><div class="checkbox">
                                <input type="checkbox" id="departmentid_<?php echo $i + 1; ?>" name="departmentcheckbox" checked class="entitycheckbox css-checkbox" />
                                <label for="departmentid_<?php echo $i + 1; ?>" class="css-label"></label>
                            </div>
                            <?php
                            if (!empty($resultantdepartment["department"]["hiddenid"][$i])) {
                                echo '<input type="hidden" value="' . $resultantdepartment["department"]["hiddenid"][$i] . '" name="data[Org][departmenthiddenid][]" class="departmentdbid">';
                            }
                            ?>
                            <input type="hidden" class="departmentactive" name="data[Org][departmentactive][]" value="<?php echo $resultantdepartment["department"]["activestatus"][$i]; ?>">
                            <input type="hidden" class="departmentsaveunsave" name="data[Org][departmentsave][]" value="<?php echo $resultantdepartment["department"]["savestatus"][$i]; ?>">
                        </td>
                        <td width="45%" class="departmentselectrow">
                            <div class = "select-style">
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
                            ?>
                        </td>
                        <td width="20%"><?php echo $this->Html->image("EditRow.png", array("class" => "editdepartment")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savedepartment")); ?></td>
                        <td width="20%"><?php echo $this->Html->image("DeleteRow.png", array("class" => "deletedepartment")); ?></td>
                    </tr>
                <?php }
                ?>
                <?php
            } else {
                $i = 1;
                if (isset($existing_departments)) {
                    foreach ($existing_departments as $id => $value) {
                        $others_department = "";
                        ?>
                        <tr id="adddepartment_<?php echo $i ?>">
                            <td width="15%"><div class="checkbox">
                                    <input type="checkbox" id="departmentid_<?php echo $i; ?>" name="departmentcheckbox" <?php echo ($existing_departmentsstatus[$id]) ? "checked" : ""; ?> class="departmentcheckbox css-checkbox" />
                                    <label for="departmentid_<?php echo $i; ?>" class="css-label"></label>
                                </div>
                                <input type="hidden" class="departmentdbid" name="data[Org][departmenthiddenid][]" value="<?php echo $id; ?>">
                                <input type="hidden" class="departmentactive" name="data[Org][departmentactive][]" value="<?php echo ($existing_departmentsstatus[$id]) ? "active" : "inactive"; ?>">
                                <input type="hidden" class="departmentsaveunsave" name="data[Org][departmentsave][]" value="save"></td>
                            <td width="45%" class="departmentselectrow"><p class='departmentv'><?php echo $value; ?></p>
                                <div class = "select-style">
                                    <select class="departmentvalues form-control" name="data[Org][departments][]" id="" style="display: none">
                                        <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                        <?php
                                        foreach ($departments as $department) {
                                            echo '<option value="' . $department . '">' . $department . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php
                                if (!in_array($value, $departments)) {
                                    $others_department = $value;
                                }
                                ?>
                                <input type="hidden" name="data[Org][department_other_department][]" class="form-control other_department" value="<?php echo $others_department; ?>"></td>
                            <td width="20%"><?php echo $this->Html->image("EditRow.png", array("class" => "editdepartment")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savedepartment")); ?></td>
                            <td width="20%"><?php echo $this->Html->image("DeleteRow.png", array("class" => "deletedepartment")); ?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
            }
            ?>
        </table>
    </div>
</section>
<!--Job Title-->
<section class="AddCoreValue">
    <div class="row AddCoreValue">
        <div class="col-md-12">
            <div class="pull-left">
                <h4>ENTER JOB TITLES</h4>
            </div>
            <div class="pull-right"><span>Add a New Job Title</span> <a href="javascript:void(0);"><?php echo $this->Html->image('addCoreValue.png', array('id' => 'addjobtitle')); ?></a></div>
        </div>
    </div>
    <?php
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
    <div class="row CreateEditOrg" id="addjobtitlediv" style="<?php echo $display; ?>">
        <table class="table table-hover" id="addjobtitletable">
            <tr>
                <th width="15%">Active</th>
                <th width="45%">Job Title</th>
                <th width="20%">Edit / Save</th>
                <th width="20%">Delete</th>
            </tr>
            <?php
            if (!empty($resultantdepartment["jobtitle"])) {
                for ($i = 0; $i < count($resultantdepartment["jobtitle"]["value"]); $i++) {
                    $dept = $this->request->data['Org']['jobtitles'];
                    ?>
                    <tr id="addjobtitle_<?php echo $i + 1; ?>">
                        <td width="15%"><div class="checkbox">
                                <input type="checkbox" id="jbactive_<?php echo $i + 1; ?>" name="jobtitlecheckbox" class="jobtitlecheckbox css-checkbox" />
                                <label for="jbactive_<?php echo $i + 1; ?>" class="css-label"></label>
                            </div>
                            <input type="checkbox" name="jobtitlecheckbox" checked class="jobtitlecheckbox" />
                            <?php
                            if (!empty($resultantdepartment["jobtitle"]["hiddenid"][$i])) {
                                echo '<input type="hidden" value="' . $resultantdepartment["jobtitle"]["hiddenid"][$i] . '" name="data[Org][jobtitlehiddenid][]" class="jobtitledbid">';
                            }
                            ?>
                            <input type="hidden" class="jobtitleactive" name="data[Org][jobtitleactive][]" value="<?php echo $resultantdepartment["jobtitle"]["activestatus"][$i]; ?>">
                            <input type="hidden" class="jobtitlesaveunsave" name="data[Org][jobtitlesave][]" value="<?php echo $resultantdepartment["jobtitle"]["savestatus"][$i]; ?>">
                        </td>
                        <td width="45%" class="jobtitleselectrow"><?php $style = ($resultantdepartment["jobtitle"]["savestatus"][$i] == "save") ? "none" : "block"; ?>
                            <div class = "select-style">
                                <select class="jobtitlevalues form-control" name="data[Org][jobtitles][]" id="" style="display: <?php echo $style; ?>">
                                    <option value="">Select Job Title</option>
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
                            ?>
                        </td>
                        <td width="20%"><?php echo $this->Html->image("EditRow.png", array("class" => "editjobtitle")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savejobtitle")); ?></td>
                        <td width="20%"><?php echo $this->Html->image("DeleteRow.png", array("class" => "deletejobtitle")); ?></td>
                    </tr>
                    <?php
                }
            } else {
                $i = 1;
                if (isset($existing_jobtitles)) {
                    foreach ($existing_jobtitles as $id => $value) {
                        $others_department = "";
                        ?>
                        <tr id="addjobtitle_<?php echo $i ?>">
                            <td width="15%"><div class="checkbox">
                                    <input type="checkbox" id="jbactive_<?php echo $i; ?>" name="jobtitlecheckbox" <?php echo ($existing_jobtitlesstatus[$id]) ? "checked" : ""; ?> class="jobtitlecheckbox css-checkbox" />
                                    <label for="jbactive_<?php echo $i; ?>" class="css-label"></label>
                                </div>
                                <input type="hidden" class="jobtitlebid" name="data[Org][jobtitlehiddenid][]" value="<?php echo $id; ?>">
                                <input type="hidden" class="jobtitleactive" name="data[Org][jobtitleactive][]" value="<?php echo ($existing_jobtitlesstatus[$id]) ? 'active' : 'inactive'; ?>">
                                <input type="hidden" class="jobtitlesaveunsave" name="data[Org][jobtitlesave][]" value="save"></td>
                            <td width="45%" class="jobtitleselectrow"><p class='jobtitlev'><?php echo $value; ?></p>
                                <div class = "select-style">
                                    <select class="jobtitlevalues form-control" name="data[Org][jobtitles][]" id="" style="display: none">
                                        <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                        <?php
                                        foreach ($jobtitles as $jobtitle) {
                                            echo '<option value="' . $jobtitle . '">' . $jobtitle . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <?php
                                if (!in_array($value, $jobtitles)) {
                                    $others_department = $value;
                                }
                                ?>
                                <input type="hidden" name="data[Org][jobtitle_other_department][]" class="form-control other_department" value="<?php echo $others_department; ?>"></td>
                            <td width="20%"><?php echo $this->Html->image("EditRow.png", array("class" => "editjobtitle")); ?> / <?php echo $this->Html->image("SaveRow.png", array("class" => "savejobtitle")); ?></td>
                            <td width="20%"><?php echo $this->Html->image("DeleteRow.png", array("class" => "deletejobtitle")); ?></td>
                        </tr>
                        <?php
                        $i++;
                    }
                }
            }
            ?>
        </table>
    </div>
</section>
<section class="container-fluid footer-bg">
    <div class="container">
        <div class="row">
            <div class="pull-right">
                <button type="button" class="btn btn-default" id="orgformcancel">Cancel</button>
                <button type="button" class="btn btn-default" id="editorgformsubmit">Save</button>
            </div>
        </div>
    </div>
</section>
<?php echo $this->Form->end(); ?> 
<script>
    $(document).ready(function () {
        var allow_comment = '<?php echo $this->request->data['Org']['allow_comments']; ?>';
        var public_endorse_visible = '<?php echo $this->request->data['Org']['public_endorse_visible']; ?>';
        if (allow_comment == 1) {
            $('.commentcompulsory, .publicEndorse').show();
        } else {
            $('.commentcompulsory, .publicEndorse').hide();
        }

        if (public_endorse_visible == 1) {
            $('#endorse_visible_alert').show();
        } else {
            $('#endorse_visible_alert').hide();
        }
    });


    $('.allowIt').on('click', function () {
        if ($('input[class=allowIt]:checked').val() == 1) {
            $('.commentcompulsory').fadeIn('medium');
            $(".publicEndorse").slideDown('medium');
        } else {
            $('.commentcompulsory').fadeOut('medium');
            $(".publicEndorse").slideUp('medium');
        }
    });

    $('.endorse-visible-alert').on('click', function () {
        if ($('input[class=endorse-visible-alert]:checked').val() == 1) {
            $('#endorse_visible_alert').fadeIn('medium');
        } else {
            $('#endorse_visible_alert').fadeOut('medium');
        }
    });

    $('.optional_comments').on('click', function () {
        if ($('input[class=optional_comments]:checked').val() == 1) {

            $('#minimum_characters_div').fadeOut('medium');
        } else {
            $("#minimum_characters_div").removeClass('hide');
            $('#minimum_characters_div').fadeIn('medium');
        }
    });


    //=============to implement colorpicker for the very first time
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

    $(document).ready(function () {
        //$('.customCoreMessage').on('click', function () {

        $('body').on('click', '.customCoreMessage', function () {

            if ($(this).is(":checked")) {
                $(this).val(1);
                $(this).closest(".custm-msg").find('.custommsgattr').slideDown();
                $(this).closest(".custm-msg").find('.custommsgattrtextarea').removeClass('hide');
//                $(this).closest(".custm-msg").find('.custommsgattrtextarea').fadeIn(250);
            } else {
                $(this).val('');
                $(this).closest(".custm-msg").find('.custommsgattr').slideUp();
                $(this).closest(".custm-msg").find('.custommsgattrtextarea').addClass('hide');
//                $(this).closest(".custm-msg").find('.custommsgattrtextarea').fadeOut(250);
            }
            //console.log($(this).attr('checked'));
        });

    });


    var stickerList = [];
    var selectedStickerList = [];
    var arr = ['GFG', 'gfg', 'g4g'];
    $(document).on("click", ".js_addSticker", function () {
        var image = $(this).attr('rel');
        //add some class on it
        if ($(this).hasClass('js_stickerAdded')) {
            $(this).removeClass('js_stickerAdded');
            $(this).find(".switchbutton").remove();
            delete stickerList[image];

            var id = $(this).attr('data-id');
            console.log(id);
            selectedStickerList.splice(selectedStickerList.indexOf(id), 1);
            console.log(selectedStickerList);
        } else {
            // Pushing the element into the array  
            var totalSelectedStickers = $(".js_stickerAdded").length;
            var id = $(this).attr('data-id');
            console.log(id);
            selectedStickerList.push(id);
            console.log(selectedStickerList);

            $(this).addClass('js_stickerAdded');
            $(this).append(' <div class="switchbutton"><img class="defaultorg" alt="" src="' + siteurl + '/img/selected-org.png"></div>');
            stickerList[image] = 1;

        }
        var selectedStickerListNew = selectedStickerList;
        selectedStickerListPut = JSON.stringify(selectedStickerListNew);
        $("#OrgSelectedStickers").val(selectedStickerListPut);
    });

    $(document).ready(function () {
        $('.js_stickerAdded').each(function (i, obj) {
            console.log($(obj).attr('data-id'));
            var id = $(obj).attr('data-id');
            selectedStickerList.push(id);
            console.log(selectedStickerList);
        });
        var selectedStickerListNew = selectedStickerList;
        selectedStickerListPut = JSON.stringify(selectedStickerListNew);
        $("#OrgSelectedStickers").val(selectedStickerListPut);

    });



    $(document).on("click", "#save_stickers", function () {

        var errorFlag = false;
        $(".err").hide();

        var title_add = $("#title_add").val();
        if (title_add == '') {
            $(".title_addErr").html('Please enter title');
            $(".title_addErr").slideDown('slow');
            $("#title_add").css('border-color', 'red');
            errorFlag = true;
        } else {
            $("#title_add").css('border-color', '');
        }

        if (errorFlag) {
            console.warn("Validation Error");
            return false;
        } else {
            console.info("Submit form now");
        }

        var imagedata = new FormData();
//        jQuery.each(jQuery('#upload_1')[0].files, function (i, file) {
//            pr(file);
//            imagedata.append('file-' + i, file);
//        });


        jQuery.each(jQuery('.uploads'), function (i, file) {
            console.log(file.files.length);
            if (file.files.length) {
                console.log(file.files[0].name);
                console.log(file.files[0]);
                imagedata.append('file-' + file.files[0].name, file.files[0]);
            }
        });
        console.log(imagedata);
//        return false;
        $("#overlay").show();
        $(".bitmojiloader").show();
        $("#save_stickers").prop('disabled', true);
        $.ajax({
            url: siteurl + "users/uploadstickers",
            type: "POST",
            data: imagedata,
            dataType: 'json',
            contentType: false,
            cache: false,
            processData: false,
            success: function (response)
            {
                console.log(response);
                if (response.status) {
                    save_stream(response.name);
                } else {
                    alert('Unable to upload sticker.');
                }
                return false;
                $("#attachmentFilesList").val(imagedata);

            },
            error: function ()
            {
            }
        });

        return false;

    });

    function save_stream(sticker_name) {
        var title = $("#title_add").val();
        var org_id = $("#organization_id").val();
        $.ajax({
            url: siteurl + "users/savestickerdata",
            type: "POST",
            data: {sticker_name: sticker_name, title: title, org_id: org_id},
            dataType: 'html',
            success: function (resposnse)
            {
                $(".bitmojiloader").hide();
                $("#save_stickers").prop('disabled', false);
                $('#myModal_addstickers').modal('hide');
                $(".sticker-container-edit").append(resposnse);
                $('#myModal_addstickers').modal('hide');

//                console.log(resposnse);
//                return false;
            },
            error: function ()
            {
            }
        });
    }

    $(".delete_sticker").on("click", function () {
        var stickerId = $(this).attr('data-id');
        console.log(stickerId);
        url = siteurl + 'ajax/deleteCustomSticker';

        bootbox.confirm({
            title: "Are you sure you want to delete sticker?",
            message: ' ',
            buttons: btnObj,
            closeButton: false,
            callback: function (result) {
                if (result == true) {
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {sticker_id: stickerId},
                        success: function (response) {
                            if (response.status == false) {
                                alertbootboxcb(response.msg, function () {

                                });
                            } else {
                                $("#custom_sticker_" + stickerId).remove();
                                alertbootboxcb("Sticker deleted!", function () {
                                });
                            }
                            $(".arrow_box").hide();
                        },
                        error: function (response) {
                        }
                    });
                }
            }
        });
    });

</script>
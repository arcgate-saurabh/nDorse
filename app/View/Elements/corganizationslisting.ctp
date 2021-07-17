<?php if (isset($orgdata["organization"]) && !empty($orgdata["organization"])) { ?>
    <?php $rec_type = $type;
    ?>
    <div class="row rec-org" >
        <div class = "col-md-12">
            <?php
            //=counter to break loop after three org
            $counter = 1;
            foreach ($orgdata["organization"] as $organizationlist) {
                //pr($organizationlist['id']);
                //====checks image, if available
                $file_headers = @get_headers($organizationlist["image"]);
                $image = ($organizationlist["image"] != "" && $file_headers[0] != 'HTTP/1.1 404 Not Found') ? $organizationlist["image"] : Router::url('/', true) . "img/big-thumb.png";
                $handClass = ($rec_type == "public" && ($organizationlist["is_request"] != 1 && $organizationlist["is_org_joined"] != 1)) ? '' : 'no-hand';
                ?>
                <div class="col-md-4 " <?php echo ($rec_type == "public" && ($organizationlist["is_request"] != 1 && $organizationlist["is_org_joined"] != 1)) ? 'style="cursor: pointer"' : ''; ?>>
                    <?php if (isset($organizationlist["is_request"]) && $organizationlist["is_request"] == 0 && isset($organizationlist["is_org_joined"]) && $organizationlist["is_org_joined"] != 1) { ?>
                        <div class="text-center rec-comp <?php echo $handClass; ?>" data-id="<?php echo $organizationlist['id']; ?>"  data-toggle="modal" data-target="#add_info_<?php echo $organizationlist['id']; ?>">
                        <?php } else { ?>
                            <div class="text-center rec-comp <?php echo $handClass; ?>">
                            <?php } ?>    
                            <div class="new">
                                <?php
                                if ($rec_type == "endorser") {
                                    echo $this->Html->link($this->Html->Image($image, array("width" => "240", "height" => "250", "class" => "img-circle")), array("controller" => "client", "action" => "orginfo", $organizationlist["id"]), array('escape' => false));
                                    if ($organizationlist["status"] == 0) {
                                        echo '<div class="inact-ribbon">' . $this->Html->Image("inact-ribbon.png") . '</div>';
                                    }
                                } else {
                                    echo $this->Html->Image($image, array("width" => "240", "height" => "250", "class" => "img-circle $handClass"));
                                }
                                ?>
                            </div>
                            <h3 class="rec-org-name">
                                <?php
                                if ($rec_type == "endorser") {
                                    echo $this->Html->link($organizationlist["name"], array("controller" => "client", "action" => "orginfo", $organizationlist["id"]));
                                } else {
                                    echo $organizationlist["name"];
                                }
                                ?>
                            </h3>
                            <?php /* ?>
                              <div class="comp-discrptn"> <?php echo $organizationlist["about"];?></div>
                              <?php */ ?>
                            <?php
                            if ($rec_type == "public") {
                                //pr($organizationlist);
                                if ($organizationlist["is_org_joined"] == 1) {
                                    echo '<button type="button" disabled="disabled" class="btn btn-orange">ALREADY JOINED</button>';
                                } else if ($organizationlist["is_request"] > 0) {
                                    echo '<button type="button" disabled="disabled" class="btn btn-orange">REQUEST SENT</button>';
                                } else if ($organizationlist["is_request"] == 0) {
                                    echo '<button style="display:none" type="button" id="joinrequestorg" data-id = ' . $organizationlist["id"] . ' class="btn btn-orange">SEND REQUEST</button>';
                                }
                            } else if ($rec_type == "endorser") {
                                echo '<div class = "switchbutton">';
                                if ($defaultorg == $organizationlist["id"]) {
                                    echo $this->Html->Image("selected-org.png", array("class" => "defaultorg", "rel" => $organizationlist["id"]));
                                } else {
                                    if ($organizationlist['status'] != 0) {
                                        echo '<button data-orgid="' . $organizationlist["id"] . '" class="swtichorg btn btn-orange" type="button">Switch Org</button>';
                                        echo '<button data-orgid="' . $organizationlist["id"] . '" class="leaveorg btn btn-blue" type="button">Leave Org</button>';
                                    }
                                }
                                echo '</div>';
                            }

                            //=check if user is a admin of an org
                            if ($rec_type == "endorser" && $organizationlist["role"] == "admin") {
                                echo '<div title="Admin for the org" class="admin-icon">' . $this->Html->Image("admin-icon.png") . '</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    if ($counter % 3 == 0) {
                        echo "</div></div>";
                        if (count($orgdata["organization"]) != $counter) {
                            echo "<div class='row rec-org'><div class='col-md-12'>";
                        }
                    }
                    ?>
                    <div id="add_info_<?php echo $organizationlist['id']; ?>" class="myModal modal fade bs-example-modal-lg add-info" tabindex="-1" role="dialog" >
                        <div class="modal-dialog joinorgform">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" >Add Info</h4>
                                </div>
                                <form class="modal-form">
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <div class="input text">
                                                <input name="" class="form-control" placeholder="First Name" type="text" value="<?php echo $loggedinUser['fname']; ?>" readonly="readonly">
                                                <input name="org_id" class="form-control" type="hidden" id="org_id">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input text">
                                                <input name="" class="form-control" placeholder="Last Name" type="text" value="<?php echo $loggedinUser['lname']; ?>" readonly="readonly">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input text">
                                                <select name="relation_to_org" id="relation_to_org" class="form-control relation_to_org">
                                                    <option value="0" disabled="disabled">Select Relationship to Org</option>
                                                    <option value="Guest">Guest</option>
                                                    <option value="Client">Client</option>
                                                    <option value="Employee">Employee</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group rto_other" id="rto_other" style="display: none;">
                                            <div class="input text">
                                                <textarea name="" class="form-control" placeholder="Relationship to Org" id="rto_other_textarea"></textarea> 
                                                <span style="color: red;display: none;" class="RTO_Other_err err">Please enter Relationship to Organization</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input text">
                                                <input name="phone_number" class="form-control" onkeypress="return isNumberKey(event);" placeholder="Phone Number" type="text" id="phone" maxlength="10">
                                                <span style="color: red;display: none;" class="phone_err err">Please enter valid mobile number</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="input text">
                                                <textarea name="message" class="form-control" placeholder="Message to Administrator" type="text" id="message"></textarea> 
                                                <span style="color: red;display: none;" class="message_err err">Please enter message</span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="modal-footer">
                                    <button class="btn btn-orange btn-block pull-left validate">Send request to join</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                }
                $counter++;
            }
            ?>
            <!--        </div>
                </div>-->
<?php //}  ?>

            <script>
                $('.myModal').on('show.bs.modal', function (event) {
                    var button = $(event.relatedTarget); // Button that triggered the modal
                    var org_id = button.data('id'); // Extract info from data-* attributes
                    var modal = $(this);
                    modal.find('#org_id').val(org_id);
                    console.log("org id : " + org_id);
                });
                $(".relation_to_org").on("change", function () {
                    var RTO = $(this).val();
                    if (RTO == 'Other') {
                        $(this).closest(".modal-form").find(".rto_other").slideDown('medium');
                    } else {
                        $(this).closest(".modal-form").find(".rto_other").slideUp('medium');
                    }
                });

                $(".validate").on('click', function () {
                    $(".err").hide();
                    var error = false;
                    var RTO = $(this).closest(".joinorgform").find("#relation_to_org").val();
                    var RTO = $(this).closest(".joinorgform").find("#relation_to_org").val();
                    var RTO_Other = $.trim($(this).closest(".joinorgform").find("#rto_other_textarea").val());
                    var phone = $.trim($(this).closest(".joinorgform").find("#phone").val());
                    var message = $.trim($(this).closest(".joinorgform").find("#message").val());
                    if (RTO == 'Other' && RTO_Other == '') {
                        //$(".RTO_Other_err").show();
                        $(this).closest(".joinorgform").find(".RTO_Other_err").show();
                        error = true;
                    }
                    if (phone.length < 10) {
                        //$(".phone_err").show();
                        $(this).closest(".joinorgform").find(".phone_err").show();
                        error = true;
                    }
                    if (message == '') {
                        //$(".message_err").show();
                        $(this).closest(".joinorgform").find(".message_err").show();
                        error = true;
                    }

                    if (!error) {
                        var Formdata = {};
                        var orgids = {};
                        var org_id = $(this).closest(".joinorgform").find("#org_id").val();
                        Formdata = {orgid: org_id, contact: phone, relation_to_org: RTO, why_want_to_join: message, relation_to_org_other: RTO_Other};
                        orgids[org_id] = Formdata;
                        var orgData = JSON.stringify(orgids);
                        console.log(orgData); //return false;
                        $.ajax({
                            type: "POST",
                            url: siteurl + 'cajax/joinrequestorg',
                            data: orgids,
                            success: function (data, textStatus, xhr) {
                                var jsonparser = $.parseJSON(data);
                                if (jsonparser["result"]["status"] == true) {
                                    $('.myModal').modal('hide');
                                    $("#orglisting .col-md-4 .rec-comp").each(function () {
                                        var elem = $(this);
                                        if (elem.children(".switchbutton").is(":visible") == true) {
                                            var elementbutton = elem.children("#joinrequestorg");
                                            elementbutton.attr("disabled", "disabled");
                                            elementbutton.removeAttr("style");
                                            elementbutton.text("REQUEST SENT");
                                            elem.children(".switchbutton").remove();
                                            elem.parent(".col-md-4").css("cursor", "")
                                        }
                                    })
                                    $(".send-multi").addClass("hidden");
                                }
                            },
                        });
                    }


                });

            </script>
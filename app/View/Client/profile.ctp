<div class="user-profile ">

    <section>
        <div class="col-md-12" style="margin-bottom:20px;">
            <?php if ($successmsg != "") { ?>
                <div id="flashmessage" class="msg text-center col-md-12" style="margin:10px 0"><?php echo $successmsg; ?></div>
            <?php } ?>
            <div class="col-md-2 text-center">
                <?php
                $nodresegivenvalue = $statesdata["giving"];
                $ndorsereceivedvalue = $statesdata["getting"] * 10;
                $tolalvalue = ($nodresegivenvalue + $ndorsereceivedvalue) % 100;
                $badgecount = floor(($nodresegivenvalue + $ndorsereceivedvalue) / 100);

                if ($profiledata["image"] == "") {
                    echo $this->Html->image('p_pic.png', array('width' => '115', 'height' => '115', 'id' => 'client_image', 'class' => 'img-circle'));
                } else {
                    $user_image = explode("/", $profiledata["image"]);

                    if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image[count($user_image) - 1])) {
                        $client_image = $user_image[count($user_image) - 1];
                        $user_image = Router::url('/', true) . PROFILE_IMAGE_DIR . $client_image;
                    } else {
                        $user_image = 'p_pic.png';
                    }

                    echo $this->Html->image($user_image, array(
                        'bigimg' => $user_image, 'index' => 0,
                        'width' => '115', 'height' => '115', 'id' => 'client_image', 'class' => 'img-circle attached-item1'));
                }
                ?>
                <!--<img src="<?php echo $imageval; ?>" bigimg ="<?php echo $bigimg; ?>"  index="<?php echo $index; ?>" type="image" width="100" class="attached-item  detail_img_<?php echo $index; ?>"  alt=""/>-->
            </div>
            <div class="col-md-3">
                <h2 class="u-name"><?php echo ucfirst(trim($profiledata["fname"] . " " . $profiledata["lname"])); ?>
                    <?php if ($logindata["id"] == $profiledata["id"]) { ?>
                        <a href="<?php echo Router::url('/', true); ?>client/editprofile"><img src="<?php echo Router::url('/', true); ?>img/edit.png" alt=""  /></a>
                    <?php } ?>
                </h2>
                <?php //if(isset($profiledata["current_org"]->job_title) && $profiledata["current_org"]->job_title!=""){  ?>

<!-- <h3 class="u-profile">(<?php echo $profiledata["current_org"]->job_title; ?>)</h3>-->
                <?php //}   ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-md-12 profile">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#personal-data" aria-expanded="false">Personal</a></li>
                <li class=""><a data-toggle="tab" href="#nDorsements-data" aria-expanded="false">nDorsements</a></li>
                <li class=""><a data-toggle="tab" href="#notification-data" aria-expanded="false">Notifications</a></li>
            </ul>
            <div class="tab-content">
                <div id="personal-data" class="tab-pane active in fade">
                    <div class="col-md-12">
                        <div class="user-profile ">
                            <div class="col-md-12">
                                <?php if ($successmsg != "") { ?>
                                    <div id="flashmessage" class="msg text-center col-md-12" style="margin:10px 0"><?php echo $successmsg; ?></div>
                                <?php } ?>
                            </div>
                            <div class="user-profile">
                                <table class="table table-hover table-striped">
                                    <tbody>
                                        <tr>
                                            <td>Name : </td>
                                            <td ><?php echo ucfirst(trim($profiledata["fname"] . " " . $profiledata["lname"])); ?></td>
                                        </tr>
                                        <?php if (isset($profiledata["dob"]) && $profiledata["dob"] != "") { ?>
                                            <tr>
                                                <td>D. O.B :</td>
                                                <td ><?php echo $profiledata["dob"]; ?></td>
                                            </tr>
                                        <?php }
                                        ?>
                                        <?php if (isset($profiledata["hobbies"]) && $profiledata["hobbies"] != "") { ?>
                                            <tr>
                                                <td>Hobbies :</td>
                                                <td ><?php echo str_replace(",", " , ", $profiledata["hobbies"]); ?></td>
                                            </tr>
                                        <?php }
                                        ?>
                                        <?php if (isset($profiledata["skills"]) && $profiledata["skills"] != "") { ?>
                                            <tr>
                                                <td>Skills :</td>
                                                <td ><?php echo str_replace(",", " , ", $profiledata["skills"]); ?></td>
                                            </tr>
                                        <?php }
                                        ?>
                                        <?php if ($profiledata["street"] != "" || $profiledata["city"] != "" || $profiledata["country"] != "" || $profiledata["zip"] != "") { ?>
                                            <tr>
                                                <td>Address :</td>
                                                <td ><?php
                                                    $address = "";
                                                    if ($profiledata["street"] != "") {
                                                        $address .= $profiledata["street"];
                                                    }

                                                    if ($profiledata["city"] != "") {
                                                        if ($address != "") {
                                                            $address .= " , ";
                                                        }
                                                        $address .= $profiledata["city"];
                                                    }
                                                    if ($profiledata["country"] != "") {
                                                        if ($address != "") {
                                                            $address .= " , ";
                                                        }
                                                        $address .= $profiledata["country"];
                                                    }

                                                    if ($profiledata["state"] != "") {
                                                        if ($address != "") {
                                                            $address .= " , ";
                                                        }
                                                        $address .= $profiledata["state"];
                                                    }
                                                    if ($profiledata["zip"] != "") {
                                                        if ($address != "") {
                                                            $address .= " , ";
                                                        }
                                                        $address .= $profiledata["zip"];
                                                    }
                                                    echo $address;
                                                    ?></td>
                                            </tr>
                                        <?php }
                                        ?>
                                        <?php if (isset($profiledata["mobile"]) && $profiledata["mobile"] != "") { ?>
                                            <tr>
                                                <td>Phone :</td>
                                                <td ><?php echo $profiledata["mobile"]; ?></td>
                                            </tr>
                                        <?php }
                                        ?>
                                    </tbody>
                                </table>
                                <?php
//                                pr($logindata['source']);
                                if ($logindata['source'] != 'ADFS') {
                                    if ($logindata["id"] == $profiledata["id"]) {
                                        ?>
                                        <a href="<?php echo Router::url('/', true); ?>client/resetpassword" class="btn btn-orange">Change Password </a>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="nDorsements-data" class="tab-pane fade">
                    <div class="col-md-12 select-date">


                        <!-- DateRange Code start -->
                        <div class="col-md-12 time-range">
                            <div class="pull-left">
                                <h3>Select a Time Range</h3>
                            </div>
<!--                            <div class="pull-right">
                                <button class="btn btn-default" id="showdatawithoutdatestas">SHOW ALL</button>
                            </div>-->
                        </div>
                        <div class="clearfix"></div>   
                        <div class="select-date col-md-12">
                            <div class="col-md-3 form-group">
                                <label> From</label>

                                <?php echo $this->Form->input('startdate', array('placeholder' => 'Start Date', 'type' => 'text', 'id' => 'datepicker_start', 'class' => 'form-control date', 'label' => false)); ?>
                            </div>
                            <div class="col-md-3 form-group">
                                <label> To</label>
                                <?php echo $this->Form->input('enddate', array('placeholder' => 'End Date', 'type' => 'text', 'id' => 'datepicker_end', 'class' => 'form-control date', 'label' => false)); ?>
                            </div>
                            <div class="col-md-6 ">
                                <button class="btn btn-default" id="statesearch">SEARCH</button>
                                <!--<button class="btn btn-default button-orange" id="showchartendorsment">SHOW CHART</button>-->

                            </div>
                        </div>
                        <!-- DateRange code end -->

                        <section id="endorse-stats">
                            <div class="ndorse-states nDorsement-profile">
                                <table class="table table-hover table-states">
                                    <tr >
                                        <th  style="color: black !important">nDorsement Given </th>
                                        <th  style="color: black !important" class="text-right"><?php echo $statesdatanew["endorse_given"]; ?></th>
                                    </tr>
                                    <tr>
                                        <th style="color: black !important">nDorsement Received </th>
                                        <th  style="color: black !important" class="text-right"><?php echo $statesdatanew["endorse_received"]; ?></th>
                                    </tr>
                                </table>
                            </div>
                        </section>
                        <div class="nDorsement-profile">
                            <div class="my-badges">
                                <div class="col-md-3">
                                    <h2>My Badges:</h2>
                                </div>


                                <?php
                                //pr($badgesData);
                                if (!empty($badgesData)) {
                                    ?>
                                    <div class="col-md-9" id="endorse-badges">
                                        <?php
                                        $inc = 1;

                                        foreach ($badgesData as $badgesval) {
                                            switch ($badgesval["trophy_id"]) {
                                                case 1:
                                                    $tooltipText = 'nDorse Badge: For being a valued team member';
                                                    break;
                                                case 2:
                                                    $tooltipText = 'nDorse Badge: Top nDorser of the Month Badge';
                                                    break;
                                                case 3:
                                                    $tooltipText = 'nDorse Badge: Top nDorsed of the Month Badge';
                                                    break;
                                                default:
                                                    $tooltipText = 'Default Badges';
                                                    break;
                                            }
                                            ?>
                                            <div class="badge-count text-center">
                                                <!--<a href="javascript:void(0);">-->
                                                <img  alt="" id="client_image" data-placement="top" data-toggle="popover" data-trigger="hover" data-content="<?php echo $tooltipText; ?>" src="<?php echo $badgesval["image"]; ?>" width="60" >
                                                <!--</a>-->
                                                <br />
                                                <?php
                                                //echo $badgesval["count"];
                                                if ($inc == 1) {
                                                    echo $badgecount;
                                                } else {
                                                    echo $badgesval["count"];
                                                }
                                                ?> </div>
                                            <?php
                                            $inc++;
                                        }
                                        ?>
                                        <!--                            <div class="badge-count text-center"> 
                                                                        <a href="javascript:void(0);" data-placement="top"  data-toggle="popover" data-trigger="focus" data-content="nDorse Badge: For being a valued team member and a positive influence at work!" >
                                                                            <img alt="" id="client_image" src="<?php echo Router::url('/', true) . "uploads/trophies/medal-green.png"; ?>" width="60">
                                                                        </a>
                                                                        <br>
                                                                        1 
                                                                    </div>
                                                                    <div class="badge-count text-center">
                                                                        <a href="javascript:void(0);" data-placement="top" data-toggle="popover" data-trigger="focus" data-content="nDorse Badge: Top nDorser of the Month Badge" >
                                                                            <img alt="" id="client_image" src="<?php echo Router::url('/', true) . "uploads/trophies/medal-orange.png"; ?>" width="60">
                                                                        </a>
                                                                        <br>
                                                                        1 
                                                                    </div>
                                                                    <div class="badge-count text-center">
                                                                        <a href="javascript:void(0);" data-placement="top" data-toggle="popover" data-trigger="focus" data-content="nDorse Badge: Top nDorsed of the Month Badge" >
                                                                            <img alt="" id="client_image" src="<?php echo Router::url('/', true) . "uploads/trophies/medal-yellow.png"; ?>" width="60">
                                                                        </a>
                                                                        <br>
                                                                        0 
                                                                    </div>-->
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="core-values-div">
                            <?php
                            if (!empty($coreValuesData)) {
                                ?>
                                <table class="table table-hover table-core-value">
                                    <tbody>
                                        <tr>
                                            <th colspan="2"><strong>Core Values Collected: </strong></th>
                                        </tr>
                                        <?php foreach ($coreValuesData as $coreval) { ?>
                                            <tr>
                                                <td><?php echo $coreval["name"]; ?></td>
                                                <td class="text-right"><?php echo $coreval["value"]; ?> </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>



                    </div>
                </div>
                <div id="notification-data" class="tab-pane fade">
                    <div class="col-md-12">
                        <div class="user-profile ">
                            <div class="col-md-12">
                                <?php if ($successmsg != "") { ?>
                                    <div id="flashmessage" class="msg text-center col-md-12" style="margin:10px 0"><?php echo $successmsg; ?></div>
                                <?php } ?>
                            </div>
                            <div class="user-profile">
                                <table class="table table-hover table-striped">
                                    <tbody>
                                        <?php
                                        if (!empty($jsonNotificationDataArray)) {
                                            foreach ($jsonNotificationDataArray as $index => $notificationDATA) {
//                                                pr($notificationDATA);
                                                ?>
                                                <tr>
                                                    <td><?php echo $this->Html->image($notificationDATA['user_image'], array('class' => 'img-circle hand show-user-profile', 'width' => "50px", 'height' => '50px')); ?> <?php echo $notificationDATA['plain_msg']; ?></td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                $('[data-toggle="popover"]').popover();
                $('.attached-item1').on('click', function () {
                    var src = $(this).attr('bigimg');
                    var img = '<img src="' + src + '" class="img-responsive no-hand"/>';
                    var html = '';
                    html += img;
                    $('#myPhotoModal').modal();
                    $('#myPhotoModal').on('shown.bs.modal', function () {
                        $('#myPhotoModal .modal-body').html(html);
                        //new code
                        //$('a.controls').trigger('click');
                    })
                    $('#myPhotoModal').on('hidden.bs.modal', function () {
                        $('#myPhotoModal .modal-body').html('');

                    });
                });

                $("#datepicker_start").datepicker(dateparameters);
                $("#datepicker_end").datepicker(dateparameters);

                $(document).on("click", "#statesearch", function () {

                    var start_date = $("#datepicker_start").val();
                    var end_date = $("#datepicker_end").val();
                    if (start_date == "") {
                        alertbootbox("Enter start date");
                        return;
                    } else if (start_date != "") {
                        var dateobj = start_date.split("-");
                        var starty = dateobj[2];
                        var startm = dateobj[0];
                        var startd = dateobj[1];
                        //  alert(start_date);
                        var d = new Date(starty, startm, startd);
                        starttime = d.getTime();
                        startdateendorse = start_date;
                        if (end_date != "") {
                            var dateobj = end_date.split("-");
                            var endy = dateobj[2];
                            var endm = dateobj[0];
                            var endd = dateobj[1];
                            var d = new Date(endy, endm, endd);
                            endtime = d.getTime();
                            if (starttime > endtime)
                            {
                                $("#datepicker_start").val("")
                                startdateendorse = "";
                                alertbootbox("end date greater than start date");
                                return;
                            }
                            enddateendorse = end_date;
                        }

                    }
                    // endorse date search
                    curl = siteurl + 'cajax/getstatesearch';
                    var formData = {start_date: start_date, end_date: end_date};

                    $.ajax({
                        url: curl,
                        type: "POST",
                        data: formData,
                        success: function (data, textStatus, jqXHR)
                        {
                            var data_Arr = String(data).split('=====');

                            $("#endorse-stats").html("");
//                            $("#endorse-badges").html("");
                            if ($.trim(data_Arr[0]) == "") {
                                $(" <div >No Data available</div>").appendTo("#endorse-stats");
                            } else {

                                $(data_Arr[0]).appendTo("#endorse-stats");
                            }

                            if ($.trim(data_Arr[1]) != "") {
                                //$(" <div >No Data available</div>").appendTo("#endorse-stats");
                                //$(data_Arr[1]).appendTo("#endorse-badges");
                            }

                            if ($.trim(data_Arr[2]) != "") {

                                //$(" <div >No Data available</div>").appendTo("#endorse-stats");

//                                $(".knob").val(data_Arr[2]);
//                                $('.knob').knob();
                            }

                        },
                        error: function (jqXHR, textStatus, errorThrown)
                        {

                        }
                    });


                });

            });

        </script>
        <div class="modal fade" id="myPhotoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="padding-bottom: 0px;">
                        <button data-dismiss="modal" class="btn btn-default pull-right close" type="button">×</button>
                        <!--                <h3 style="margin-bottom: -20px;">Gallery</h3>-->
                    </div>
                    <div class="modal-body">                
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
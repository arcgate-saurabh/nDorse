<?php
//pr($announcementData); exit;
$data = array(
    "textcenter" => "Pending Announcements",
    "righttabs" => "1"
);
echo $this->Element('headerorg', array('data' => $data));
?>
<!--Conatiner start here-->

<div class="settings"> <?php echo $this->Session->flash('auth'); ?>
    <p id="flashmessage"><?php echo $this->Session->Flash(); ?></p>
    <div class="col-md-12">

        <?php //pr($announcementData); ?>
        <section>

            <?php
            // echo "test"; exit;

            if (!empty($announcementData)) {
                ?>
                <!--                                    <div class="search-icn ">
                                                        <input type="text" class="form-control" id="searchliveendorsements"  placeholder="Filter Items..." >
                                                        <div id="livesearch"></div>
                
                                                    </div>-->

                <?php
            } else {
                echo '<div class="containerorg lady-lake"><div class="nodataavailable">No Pending Announcements</div></div>';
            }
            ?>
            <!--<input type="hidden" id="endorsementorgid" value="<?php //echo $orgdata['Organization']['id'];          ?>">-->
        </section>
        <?php // pr($announcementData);  ?>
        <div id="searchendorsement">
            <?php
///===================element to search data for live endorsements 
            if (!empty($announcementData)) {
                
                ?>
                <!--<div class="col-md-12"><div class="msg">Pending Announcements</div></div>-->
                <div class="clearfix"></div>
                <?php
                foreach ($announcementData as $index => $announcements) {
//                    pr($announcements);
//                    exit;
                    ?>
                    <section class="lady-lake farhan hand pending-announcement-admin"  id="announcement_<?php echo $announcements['Announcement']["id"]; ?>" announcement_id ="<?php echo $announcements['Announcement']["id"]; ?>">
                        <div class="row">
                            <div class="col-md-3 pull-left" >
                                <div class="col-md-8 text-center">
                                    <?php
                                    if ($announcements["User"]["image"] != "" && file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $announcements["User"]["image"])) {
                                        $profile_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . $announcements["User"]["image"];
                                        $image = $this->Html->image($profile_imagenew, array('width' => '64', 'height' => '64', 'id' => 'org_image', 'class' => 'img-circle no-hand'));
                                    } else {
                                        $image = $this->Html->Image("user.png", array("class" => "img-circle no-hand", "alt" => "64x64", "width" => "64px", "height" => "64px"));
                                    }
                                    ?>
                                    <?php echo $image; ?> <!--<img alt="" data-src="holder.js/64x64" class="media-object" style="width: 64px; height: 64px;" src="img/user.svg" data-holder-rendered="true"> -->
                                    <div class="far-user "><?php echo base64_decode($announcements['User']['fname'])." ".base64_decode($announcements['User']['lname']); ?></div>

                                </div>
                            </div>
                            <div class="col-md-6 text-center hand" style="margin:20px 0;">
                                <?php echo trim($announcements['Announcement']["message"]); ?>

                                <div class="detail-img" style="font-size: 14px;">
                                    <p>Scheduled Time : <?php echo $announcements['Announcement']['scheduled_datetime']; ?> </p>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="menu-down"><?php echo $this->Html->image('menu-down.png', array('class' => 'show-options', 'align' => 'right')) ?>
                                    <div class="clearfix"></div>
                                    <div class="menu-cont">
                                        <ul>
                                            <!--                                                    <a href="javascript:void(0);">-->
                                            <li class="delete-post hand delete-announcement"  data-announcement-id="<?php echo $announcements['Announcement']['id']; ?>">Delete this announcement</li>
                                            <!--</a>-->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 orange-bg">

                                <div class="col-md-3 pull-left">

                                </div>
                                <div class="col-md-6 text-center">
                                    <h4 class="" style="margin:4px 0">
                                        <?php
                                        //=========calculating time difference from present time.
                                        $createddate = new DateTime($announcements['Announcement']["created"]);
                                        $now = new DateTime();
                                        $timediff = (array) $now->diff($createddate, true);
                                        $arraytimediff = array("y" => "year", "m" => "month", "d" => "days", "h" => "hr", "i" => "minute", "s" => "second",);
                                        foreach ($timediff as $key => $difference) {
                                            if ($difference != 0) {
                                                $diffkey = $arraytimediff[$key];
                                                if ($key == "h" || $key == "i" || $key == "s") {
                                                    $plural = ($difference <= 1) ? "" : "s";
                                                    echo $difference . " " . $diffkey . $plural . " ago";
                                                } else {
                                                    echo date("M d", strtotime($announcements['Announcement']["created"]));
                                                }
                                                break;
                                            }
                                        }
                                        ?>
                                    </h4>
                                </div>
                                <div class="col-md-3 ">
                                    <div class="col-md-10 text-center">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php
                }
            }
            ?>

        </div>
    </div>
    <script>
        $(document).ready(function () {
            $("#reset_setting").click(function () {
                location.href = '<?php echo $prev_page; ?>';
            });
            /*var formsubmitted = window.location.hash.substr(1);
             $('.nav-tabs a[href="#' + formsubmitted + '"]').tab('show');
             $('html, body').animate({
             'scrollTop': $("p#flashmessage").position().top
             });*/

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


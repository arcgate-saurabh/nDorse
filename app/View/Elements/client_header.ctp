<?php
if ($this->Session->read('Auth.User.id')) {
    $loggedUserAuth = ($this->Session->read('Auth'));
    $loggedUserNotifications = $this->Session->read('Auth.Notifications');
//    pr($loggedUserN); exit;
    $loggedUserRole = ($this->Session->read('Auth.User.role'));
    $current_org = $this->Session->read('Auth.User.current_org');
//    pr($current_org->id);exit;
    $orgUpdates = $this->Session->read('Auth.User.org_updates');
    //pr($current_org->show_leader_board); exit;
    $check_current_org = 0;
//    pr($orgUpdates);
//    exit;

    if (!empty($current_org) && ($orgUpdates['org_status'] == 'active' && $orgUpdates['user_status'] == 'active')) {
        $check_current_org = $current_org->id;
    }
    $fname = ucfirst($this->Session->read('Auth.User.fname'));
    $lname = ucfirst($this->Session->read('Auth.User.lname'));
    $FullName = $fname . " " . $lname;
    $image = $this->Session->read('Auth.User.image');
    $source = $this->Session->read('Auth.User.source');
//    $loggedUserAuth['User']['source']
} else {
    $orgUpdates['org_status'] = 'inactive';
    $orgUpdates['user_status'] = 'inactive';
}
$currentpage = $this->here;

$ndorse_home = "ndorse-home.png";
$ndorse_ndorse = "nDorser.png";
$ndorse_ndorsed = "nDorsed.png";
$ndorse_stats = "nDorsements.png";
$notification = "notification-white.png";
$ndorsements = "nDorsements.png";

//$ndorse_ndorse=
if (strstr($currentpage, "endorse")) {
    $pageval = explode("endorse", $currentpage);
//  print_r($pageval);exit;
    $paramsactionendorse = trim($this->params["action"]);
    if ($paramsactionendorse == "ndorse") {
        $ndorse_ndorse = "nDorser-act.png";
    } elseif ($paramsactionendorse == "ndorsed") {
        $ndorse_ndorsed = "nDorsed-act.png";
    } elseif ($paramsactionendorse == "stats") {
        $ndorse_stats = "nDorsements-act.png";
    } elseif ($paramsactionendorse == "index") {
        $ndorse_home = "ndorse-home-act.png";
    } elseif ($paramsactionendorse == "summary") {
        $ndorsements = "nDorsements-act.png";
    }
}
$myProfile = "myprofile-icon-white.png";
if (strstr($currentpage, "client")) {
    $pageval = explode("client", $currentpage);
//  print_r($pageval);exit;
    $paramsactionendorse = trim($this->params["action"]);
    if ($paramsactionendorse == "notifications") {
        $notification = "notification-active-white.png";
    } elseif ($paramsactionendorse == "profile") {
        $myProfile = "myprofile-icon-active.png";
    }
}
$paramsaction = trim($this->params["action"]);
?>
<?php if (!isset($noLeftMenu) || !$noLeftMenu) { ?>

    <div class="col-sm-3 col-md-2 client-nav sidebar ">
        <div id="wrapper" class="nano">
            <div class="content">
                <?php /*  <ul class="sidebar-nav">
                  <?php if ($check_current_org > 0) { ?>
                  <li class="sidebar-brand"> <?php echo $this->Html->Link("Stats and Reports", "javascript:void(0);", array('style' => "cursor: default !important; padding:0 0 10px 10px")); ?>
                  <ul class="with-arrows">
                  <?php if ($check_current_org != 0) { ?>
                  <li class="sidebar-brand">
                  <a href="javascript:void(0);" data-toggle="modal" data-target=".manager-report-popupmodel">User Report</a>
                  <?php //echo $this->Html->Link("User Report", array("controller" => "client", "action" => "managerreport", "id" => $check_current_org), ($paramsaction == "managerreport") ? array("class" => "active-link", 'target' => '_blank', 'escape' => false) : array("class" => "", 'target' => '_blank', 'escape' => false)); ?>

                  </li>
                  <?php } ?>
                  <?php /*  if ($loggedUserRole == 2) { ?>
                  <li class="sidebar-brand"> <?php echo $this->Html->Link("What's New", array("controller" => "client", "action" => "whatsnew"), ($paramsaction == "whatsnew") ? array("class" => "active-link") : ""); ?> </li>
                  <?php }
                  ?>

                  <?php
                  //                                pr($current_org);
                  if ($current_org->show_leader_board == 1) {
                  //Commented after discuss with Rohan and bryan @13-july-2018 mail
                  ?>
                  <!--<li class="sidebar-brand"> <?php echo $this->Html->Link("Leader Board", array("controller" => "client", "action" => "leaderboard"), ($paramsaction == "leaderboard") ? array("class" => "active-link") : ""); ?> </li>-->
                  <?php } ?>
                  <li class=""> <?php echo $this->Html->Link("nDorsement History By Day", array("controller" => "endorse", "action" => "day"), ($paramsaction == "day") ? array("class" => "active-link") : ""); ?></li>
                  <li class=""> <?php echo $this->Html->Link("nDorsement History By Department", array("controller" => "endorse", "action" => "departments"), ($paramsaction == "departments") ? array("class" => "active-link") : ""); ?></li>
                  <li class=""> <?php echo $this->Html->Link("nDorsement History By Job Title", array("controller" => "endorse", "action" => "jobtitle"), ($paramsaction == "jobtitle") ? array("class" => "active-link") : ""); ?></li>
                  <li class=""> <?php echo $this->Html->Link("nDorsement History By Sub Org", array("controller" => "endorse", "action" => "entity"), ($paramsaction == "entity") ? array("class" => "active-link") : ""); ?></li>

                  <?php */ /* ?>
                  </ul>
                  </li>
                  <?php } ?>
                  <li class="sidebar-brand"> <?php echo $this->Html->Link("Organizations", "javascript:void(0);", array('style' => "cursor: default !important; padding:0 0 10px 10px")); ?>
                  <ul class="with-arrows">
                  <li class=""> <?php echo $this->Html->Link("My Organizations", array("controller" => "client", "action" => "myorganizations"), ($paramsaction == "myorganizations") ? array("class" => "active-link") : ""); ?> </li>
                  <!--<li class=""> <?php //echo $this->Html->Link("Create Org", array("controller" => "client", "action" => "createorg"), ($paramsaction == "createorg") ? array("class" => "active-link") : "");                                          ?> </li>-->
                  <?php if (isset($loggedUserAuth['User']['profile_completed']) && $loggedUserAuth['User']['profile_completed'] != '') { ?>
                  <li class=""> <?php echo $this->Html->Link("Create Org", array("controller" => "client", "action" => "createorg"), ($paramsaction == "createorg") ? array("class" => "active-link") : ""); ?> </li>
                  <?php } else { ?>
                  <li class=""> <a href="javascript:void(0);" id="createorglinkclient" <?php ($paramsaction == "joinanorganization") ? array("class" => "active-link") : "" ?>>Create Org </a></li>
                  <?php } ?>
                  <li class=""> <?php echo $this->Html->Link("Join An Org", array("controller" => "client", "action" => "joinanorganization"), ($paramsaction == "joinanorganization") ? array("class" => "active-link") : ""); ?></li>
                  </ul>
                  </li>
                  <!--                    <li class="sidebar-brand"> <?php echo $this->Html->Link("Admin", "javascript:void(0);", array('style' => "cursor: default !important; padding:0 0 10px 10px")); ?>
                  <ul class="with-arrows">
                  <li class="disabled"> <?php echo $this->Html->Link("Manage Users", "", array("class" => "popup-for-admin")); ?> </li>
                  <li class="disabled"> <?php echo $this->Html->Link("Manage Organization", "", array("class" => "popup-for-admin")); ?> </li>
                  <li class="disabled"> <?php echo $this->Html->Link("Invite Users", "", array("class" => "popup-for-admin")); ?></li>
                  </ul>
                  </li>-->

                  <!-- Pending post added by Babulal Prasad @18-dec-2017 -->
                  <?php if ($current_org->org_role == 'admin') { ?>
                  <li class="sidebar-brand"> <?php echo $this->Html->Link("Pending Posts", array("controller" => "post", "action" => "pending"), ($paramsaction == "pending") ? array("class" => "active-link") : ""); ?></li>
                  <?php } ?>

                  <!-- Active User List added by Babulal Prasad @19-march-2018 <sup><span style='color:red;'>New</span><sup>-->
                  <li class="sidebar-brand"> <?php
                  ($paramsaction == "userlist") ? $activeuserClass = "active-link" : $activeuserClass = "";
                  echo $this->Html->Link("Active User List ", array("controller" => "endorse", "action" => "userlist"), array("class" => $activeuserClass, 'escape' => false));
                  ?></li>
                  <!--<li class="sidebar-brand"> <?php echo $this->Html->Link("My Profile", array("controller" => "client", "action" => "profile"), ($paramsaction == "profile") ? array("class" => "active-link") : ""); ?></li>-->
                  <li class="sidebar-brand"> <?php echo $this->Html->Link("Feedback", "mailto:" . SUPPORTEMAIL . "?Subject=Feedback", array("target" => "_top")); ?></li>
                  <li class="sidebar-brand"> <?php echo $this->Html->Link("Recommend nDorse", "", array("class" => "recommendLnk")); ?></li>
                  <li class="sidebar-brand"> <?php echo $this->Html->Link("FAQ", array("controller" => "client", "action" => "faq"), ($paramsaction == "faq") ? array("class" => "active-link") : ""); ?></li>
                  <li class="sidebar-brand"> <?php echo $this->Html->Link("End User License Agreement", array("controller" => "client", "action" => "tnc"), ($paramsaction == "tnc") ? array("class" => "active-link") : ""); ?></li>
                  <?php //if ($source == 'ADFS') { ?>
                  <!--<li class="sidebar-brand"> <a href="https://sso.ndorse.net/simplesaml/module.php/core/authenticate.php?as=ndorse-sp&logout">Log out</a></li>-->
                  <?php //} else { ?>
                  <li class="sidebar-brand"> <?php echo $this->Html->Link("Log out", array("controller" => "client", "action" => "logout")); ?></li>
                  <?php //} ?>
                  </ul> */ ?>


                <ul class="icnNewMenu sidebar-nav">
                    <?php if ($check_current_org > 0) { ?>

                        <?php if ($check_current_org != 0) { ?>
                            <li class=""> 
                                <a href="javascript:void(0);" data-toggle="modal" data-target=".manager-report-popupmodel"><span class="flaticon-notepad"></span>User<br/>Reports</a>

                                <?php //echo $this->Html->Link("User Report", array("controller" => "client", "action" => "managerreport", "id" => $check_current_org), ($paramsaction == "managerreport") ? array("class" => "active-link", 'target' => '_blank', 'escape' => false) : array("class" => "", 'target' => '_blank', 'escape' => false)); ?> 

                            </li>
                        <?php } ?>


                    <?php } ?>

                    <li>
                        <?php echo $this->Html->Link("<span class='flaticon-user-4'></span>My<br/>Organizations", array("controller" => "client", "action" => "myorganizations"), ($paramsaction == "myorganizations") ? array("class" => "active-link", "escape" => false) : array("escape" => false)); ?> 

                    </li>

                                                                                        <!--<li class=""> <?php //echo $this->Html->Link("Create Org", array("controller" => "client", "action" => "createorg"), ($paramsaction == "createorg") ? array("class" => "active-link") : "");                                                                  ?> </li>-->
                    <?php if (isset($loggedUserAuth['User']['profile_completed']) && $loggedUserAuth['User']['profile_completed'] != '') { ?>
                        <li class=""> <?php echo $this->Html->Link("<span class='flaticon-edit-1'></span>Create<br/>an Org", array("controller" => "client", "action" => "createorg"), ($paramsaction == "createorg") ? array("class" => "active-link", "escape" => false) : array("escape" => false)); ?> </li>
                    <?php } else { ?>
                        <li class=""> 
                            <a href="javascript:void(0);" id="createorglinkclient" <?php ($paramsaction == "joinanorganization") ? array("class" => "active-link") : "" ?>><span class="flaticon-edit-1"></span>Create<br/>an Org</a> </a>
                        </li>
                    <?php } ?>
                    <li class=""> 

                        <?php echo $this->Html->Link("<span class='flaticon-users'></span>Join<br/>an Org", array("controller" => "client", "action" => "joinanorganization"), ($paramsaction == "joinanorganization") ? array("class" => "active-link", "escape" => false) : array("escape" => false)); ?>

                    </li>


                    <!-- Pending post added by Babulal Prasad @18-dec-2017 -->
                    <?php if ($current_org->org_role == 'admin') { ?>
                        <li class=""> 
                            <?php echo $this->Html->Link("<span class='flaticon-file-2'></span>Pending<br/>Post", array("controller" => "post", "action" => "pending"), ($paramsaction == "pending") ? array("class" => "active-link", "escape" => false) : array("escape" => false)); ?>
                        </li>
                    <?php } ?>


                    <li class=""> <?php
                        ($paramsaction == "userlist") ? $activeuserClass = "active-link" : $activeuserClass = "";
                        echo $this->Html->Link("<span class='flaticon-users-1'></span>Active<br/>User List", array("controller" => "endorse", "action" => "userlist"), array("class" => $activeuserClass, 'escape' => false));
                        ?></li>
                    <!--<li class="sidebar-brand"> <?php echo $this->Html->Link("My Profile", array("controller" => "client", "action" => "profile"), ($paramsaction == "profile") ? array("class" => "active-link") : ""); ?></li>-->
                    <li class=""> <?php echo $this->Html->Link("<span class='flaticon-notepad-2'></span>Feedback", "mailto:" . SUPPORTEMAIL . "?Subject=Feedback", array("target" => "_top", "escape" => false)); ?></li>
                    <!--<li class="sidebar-brand"> <?php // echo $this->Html->Link("Recommend nDorse", "", array("class" => "recommendLnk"));                      ?></li>-->
                    <li class=""> <?php echo $this->Html->Link("<span class='flaticon-notepad'></span>FAQ", array("controller" => "client", "action" => "faq"), ($paramsaction == "faq") ? array("class" => "active-link", "escape" => false) : array("escape" => false)); ?></li>
                    <li class=""> <?php echo $this->Html->Link("<span class='flaticon-id-card-3'></span>End User License Agreement", array("controller" => "client", "action" => "tnc"), ($paramsaction == "tnc") ? array("class" => "active-link", "escape" => false) : array("escape" => false)); ?></li>
                    <?php //if ($source == 'ADFS') { ?>
                    <!--<li class="sidebar-brand"> <a href="https://sso.ndorse.net/simplesaml/module.php/core/authenticate.php?as=ndorse-sp&logout">Log out</a></li>-->
                    <?php //} else { ?>
                    <li class=""> <?php echo $this->Html->Link("<span class='flaticon-locked'></span>Log out", array("controller" => "client", "action" => "logout"), array("escape" => false)); ?></li>
                    <?php //} ?>
                </ul>
                <?php
                //====don't delete
                /* ?>
                  <div class="logout"> <span class="userName"> <?php echo $FullName; ?>
                  <div class="clearfix"></div>
                  <?php echo $this->Html->Link("LOGOUT", array("controller" => "client", "action" => "logout"), array("class" => "logoutText")); ?> </span> <span class="">
                  <?php

                  if ($image != "") {
                  $file_headers = @get_headers($image);
                  $image = ($file_headers[0] != 'HTTP/1.1 404 Not Found') ? $image: Router::url('/', true)."img/user.png";
                  echo '<img width="61" height="61" alt="" class="img-circle" src="'.$image.'">';
                  } else {
                  echo $this->Html->image("user.png", array("class" => "img-circle", 'width' => '61', 'height' => '61'));
                  }
                  ?>
                  </span>
                  </div>
                  <?php */
                ?>

                <!--<ul class="icnNewMenu sidebar-nav">-->
    <!--                    <li><a href="#"><span class="flaticon-notepad"></span>Maneger<br/>Reports</a></li>-->
                    <!--<li><a href="#"><span class="flaticon-users-1"></span>Active<br/>User List</a></li>-->
                    <!--<li><a href="#"><span class="flaticon-file-2"></span>Pending<br/>Post</a></li>-->
                    <!--<li><a href="#"><span class="flaticon-user-4"></span>My<br/>Organigation</a></li>-->
                    <!--<li><a href="#"><span class="flaticon-users"></span>Join<br/>an Org</a></li>-->
                    <!--<li><a href="#"><span class="flaticon-edit-1"></span>Create<br/>an Org</a></li>-->
                    <!--<li><a href="#"><span class="flaticon-notepad-2"></span>Feedback</a></li>-->
                    <!--<li><a href="#"><span class="flaticon-id-card-3"></span>EULA</a></li>-->
                    <!--<li><a href="#"><span class="flaticon-locked"></span>Logout</a></li>-->
                <!--</ul>-->
            </div>
            <div class="clearfix"></div>
            <div class="poweredBy headerFooterBG">
                <div class="client-logo">
                    <?php
                    $orgImage = 'big-thumb.png';
                    if (isset($current_org->image) && $current_org->image != '') {
                        $orgImage = $current_org->image;
                    }
                    echo $this->Html->image($orgImage, array("width" => "35", "alt" => "img", "class" => "like-img like-img-post"));
                    ?>  
                </div>
                <div class="poweredByLogo">
                    Powered By: nDorse                
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<div class="">
    <nav class="navbar navbar-fixed-top">
        <div class="header-bg-nav container-fluid headerFooterBG">
            <div>
                <div class="pull-left menu">
                    <div class="title-org">
                        <span style="margin-top:20px; font-size:16px;">
                            <?php
                            if (isset($MenuName)) {
                                echo $MenuName;
                            }
                            ?>
                        </span>
                    </div>
                </div>
                <div class="pull-right visible-xs">
                    <div class="menu-btn">
                      <!-- <img src="<?php echo Router::url('/', true); ?>img/menu_blue.png" alt="" class="menu-on"  /> -->
                    </div>
                </div>
            </div>
            <div class="clearfix visible-xs"></div>
            <div class="RightTabs">

                <?php
                /* } else { ?>
                  <span><img src="<?php echo Router::url('/', true); ?>img/add-nDorse-act.png" alt="" /></span>
                  <?php
                  } */
                if ($check_current_org > 0) {
                    ?>
                    <span>
                        <span class="count hidden" id="liveCount"></span>
                        <?php
                        $liveFeedUrl = Router::url('/', true) . 'endorse';
//                        if ($source == 'ADFS') {
//                            $liveFeedUrl = Router::url('/', true) . 'endorse/ADFSLiveFeed';
//                        }
                        ?>
                        <!--<a href="<?php //echo Router::url('/', true);                                           ?>endorse" class="endorse-home" >-->
                        <a href="<?php echo $liveFeedUrl; ?>" class="endorse-home" >
                            <img src="<?php echo Router::url('/', true); ?>img/<?php echo $ndorse_home; ?>" alt=""  />
                        </a>
                    </span>
                    <span>
                        <a href="<?php echo Router::url('/', true); ?>endorse/summary"> <img src="<?php echo Router::url('/', true); ?>img/<?php echo $ndorsements; ?>" alt=""  /> </a>
                    </span>
<!--                    <span>
                        <a href="<?php echo Router::url('/', true); ?>client/notifications"> <img src="<?php echo Router::url('/', true); ?>img/<?php echo $notification; ?>" alt=""  /> </a>
                    </span>-->
                    <!-- Added by Babualal prasad to show users notifications -->

                    <span class="dropdown">
                        <a href="javascript:void(0)" class="dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <img src="<?php echo Router::url('/', true); ?>img/<?php echo $notification; ?>" alt=""  /> </a>
                        <?php if (!empty($loggedUserNotifications)) { ?>
                            <div class="dropdown-menu user-profile" aria-labelledby="dropdownMenuButton">
                                <table class="table table-hover table-striped">
                                    <tbody>
                                        <?php
                                        foreach ($loggedUserNotifications as $index => $notifications) {
//                                            pr($notifications);
                                            ?>
                                            <tr>
                                                <td>

                                                    <?php

                                                    if ($notifications['feed_type'] != 'null') {
                                                        $rootUrl = Router::url('/', true);
                                                        if ($notifications['feed_type'] == 'ndorse') {
                                                            $link = $rootUrl . "endorse/details/" . $notifications['feed_id'];
                                                        } else {
                                                            $link = $rootUrl . "post/details/" . $notifications['feed_id'];
                                                        }
                                                        ?>
                                                        <a href="<?php echo $link; ?>" style="color: beige;text-decoration: none;">
                                                            <?php
                                                            echo $this->Html->image($notifications['user_image'], array('class' => 'img-circle hand show-user-profile', 'width' => "50px", 'height' => '50px'));
                                                            echo '<span style="margin-left: 20px;">' . $notifications['plain_msg'] . "</span>";
                                                            ?>
                                                        </a>                                    
                                                        <?php
                                                    } else {
                                                        echo $this->Html->image($notifications['user_image'], array('class' => 'img-circle hand show-user-profile', 'width' => "50px", 'height' => '50px'));
                                                        echo '<span style="margin-left: 20px;">' . $notifications['plain_msg'] . "</span>";
                                                    }
                                                    ?>



<!--                                                    <a href="#" style="color: beige;text-decoration: none;">
                                                        <img class="img-circle hand show-user-profile" width="40px" height="40px" src="<?php echo $notifications['user_image']; ?>" />
                                                        <span style="margin-left: 10px;"><?php echo $notifications['user_name']; ?></span>
                                                    </a>-->
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>



                                    </tbody>
                                </table>
                            </div>
                        <?php } else {
                            ?>
                            <div class="dropdown-menu user-profile" aria-labelledby="dropdownMenuButton">
                                <table class="table table-hover table-striped">
                                    <tbody>
                                        <tr>
                                            <td>No new notifications</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php }
                        ?>

                    </span>

                    <span>
                        <a href="<?php echo Router::url('/', true); ?>client/profile"> <img src="<?php echo Router::url('/', true); ?>img/<?php echo $myProfile; ?>" alt=""  /> </a>
                    </span>

                                                            <!--                    <span>
                                                                                    <span class="count hidden" id="ndorsedCount">                    </span>
                                                                                    <a href="<?php echo Router::url('/', true); ?>endorse/ndorsed" class="endorse-ndorsed">
                                                                                        <img src="<?php echo Router::url('/', true); ?>img/<?php echo $ndorse_ndorsed; ?>" alt="" />
                                                                                    </a>
                                                                                </span>-->

                    <!--     Hide after client ask to version 6.5.1 ticket no = 229
                      <span>  
                          <!--<span class="count">00</span>-->
                    <!--        <a href="<?php //echo Router::url('/', true);                           ?>endorse/stats"> <img src="<?php //echo Router::url('/', true);                           ?>img/<?php //echo $ndorse_stats;                           ?>" alt=""   />
                            </a>
                        </span>
                    -->
    <!--                    <span>
                      <span class="count">00</span>
                        <a href="<?php echo Router::url('/', true); ?>endorse/ndorse"> <img src="<?php echo Router::url('/', true); ?>img/<?php echo $ndorse_ndorse; ?>" alt=""   /> 

                        </a>
                    </span>-->
                    <?php if ($current_org->enable_daisy_portal == 1) { ?>
                        <span>
                            <a href="<?php echo Router::url('/', true); ?>endorse/daisy"> <img src="<?php echo Router::url('/', true); ?>img/daisy-icon.png" alt=""  /> </a>
                        </span>
                    <?php } ?>

                <?php } ?>
                <?php if ($check_current_org > 0) { ?>
                    <span id="addNDorsePost">
                        <img src="<?php echo Router::url('/', true); ?>img/add-nDorse.png" alt=""  />
                        <div class="collapse PopDown" id="" >
                            <div class="popDownArrow text-center"><?php echo $this->Html->image('popDownArrow.png'); ?></div>
                            <div class="nD-menu-well">
                                <ul class="" style="list-style:none">
                                    <li><a href="javascript:void(0);" class="ndorse-now-top" xdata-toggle="modal" xdata-target=".endorse-now-popupmodel">nDorse Now!</a></li>


                                    <?php
                                    if (isset($current_org->only_admin_post) && $current_org->only_admin_post == 1) {
                                        if ($current_org->org_role == 'admin') {
                                            ?>
                                            <li>
                                                <?php echo $this->Html->link('Post Now!', array('controller' => 'post', 'action' => 'add')); ?>
                                            </li>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <li>
                                            <?php echo $this->Html->link('Post Now!', array('controller' => 'post', 'action' => 'add')); ?>
                                        </li>
                                    <?php } ?>

                                    <!--                                <li>
                                    <?php //echo $this->Html->link('DAISY Award!', array('controller' => 'endorse', 'action' => 'daisy'));        ?>
                                                                    </li>-->
                                </ul>
                            </div>
                        </div>
                    </span>
                <?php } ?>
                <span class="hidden-xs">
                    <a href="javascript:void(0);"><?php echo $this->Html->Image("refresh.png", array('id' => 'refresh')); ?></a>
                </span>
                <?php
                if ($check_current_org > 0) {
//                    echo $current_org->org_role;
                    if ($current_org->org_role == 'admin' || $current_org->org_role == "elite") {
                        ?>
                        <span class="hidden-xs">
                            <a href="<?php echo Router::url('/', true); ?>organizations"><?php echo $this->Html->Image("admin.png", array('id' => 'refresh', 'alt' => 'Admin')); ?></a>
                        </span>
                        <?php
                    }
                }
                ?>
                <?php //echo $this->Html->Image("add-nDorse.png") ;             ?>
                <div class="clearfix"></div>
            </div>
        </div>
    </nav>
</div>
<div class="modal fade bs-example-modal-lg nDorse-process select-type endorse-now-popupmodel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"> 
              <!--        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--> 
                <!--<h4 class="modal-title select-type" >Select Type</h4> --> 
            </div>
            <div class="modal-body">
                <?php //echo $this->Form->create('endorsementType', array('class' => 'select-type', 'url' => Router::url('/', true) . "post/add"));     ?> 
                <?php
                $rootUrl = Router::url('/', true);

                if ((strpos($rootUrl, 'localhost') == '') || (strpos($rootUrl, 'localhost') < 0)) {
                    //$rootUrl = str_replace("http", "https", $rootUrl);
                    //Added by saurabh on 23/06/2021
                    //$rootUrl = preg_replace("/^http:/i", "https:", $rootUrl);
                }
                if (strpos($rootUrl, 'staging') > 0) {
                    //$rootUrl = str_replace("https", "http", $rootUrl);
                }
                echo $this->Form->create('endorsementType', array('class' => 'select-type', 'url' => $rootUrl . "endorse/add"));
                ?> 
                <?php if ($orgUpdates['user_status'] == 'active' && $orgUpdates['org_status'] == 'active') { ?>
                    <span class="radio">
                        <div class="input radio">
                            <input id="selected_user_id" value="" type="hidden" name="data[userid]"/>
                            <input type="radio" checked="checked"  id="pn" name="data[type]" value="standard">
                            <label for="pn">Public nDorsement </label>
                            <img src="<?php echo Router::url('/', true); ?>img/public-nDorse.png"  class="pull-right" alt=""  /> <br />
                            <br />
                            <input type="radio"  id="an" name="data[type]" value="anonymous">
                            <label for="an">Anonymous nDorsement </label>
                            <img src="<?php echo Router::url('/', true); ?>img/anonymous-nDorse.png"  class="pull-right" alt=""  /> <br />
                            <br />
                            <input type="radio"  id="privet" name="data[type]" value="private">
                            <label for="privet">Private nDorsement </label>
                            <img src="<?php echo Router::url('/', true); ?>img/privet-nDorse.png"  class="pull-right" alt=""  /> </div>
                    </span> 
                <?php } else { ?>
                    <div class="not-assigned" style="color:#333;">Currently, you have not been assigned an Organization. Please create, join or switch to an Organization. Go to MENU to "Create and/or Join An Org" and then switch to an Organization to set your default Organization.</div>
                <?php } ?>
            </div>
            <div class="modal-footer" style="margin-left:18px;">
                <?php if ($orgUpdates['user_status'] == 'active' && $orgUpdates['org_status'] == 'active') { ?>
                    <button class="btn btn-default pull-left" type="submit">Proceed </button>
                <?php } ?>
                <button class="btn btn-default pull-left" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    Cancel
                </button>
            </div>
            <?php echo $this->Form->end(); ?> </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg nDorse-process select-type manager-report-popupmodel" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 50%;">
            <div class="contents" style="margin-left: 16%;">
                <div class="modal-header"> 
                    <h4>Enter Code</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="orgId" value="<?php echo $check_current_org; ?>">
                    <input type="text" tabindex="0" class="form-group" id="manager_code" placeholder="ex:1234" style="margin-left: 2%;margin-top: 3%;color: black;">
                    <p class="blankerror error hide" style="margin-left: 10px;">Enter code</p>
                    <p class="validationerror error hide" style="margin-left: 10px;">Invalid code.</p>
                </div>
                <div class="modal-footer" style="margin-left:18px;">
                    <!--<a id="managerreportlink" href="/" target="_blank">TEST LINK</a>-->
                    <button class="btn btn-default pull-left managerReportCodeValidation" type="submit" style="margin-left: -20px;">Proceed </button>
                    <button class="btn btn-default pull-left" type="button" class="close" data-dismiss="modal" aria-label="Close">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="modal fade" id="recommendModal" role="dialog">
    <div class="modal-dialog"> 
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg-or">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Recommend nDorse</h4>
            </div>
            <div class="modal-body text-center">
                <span class='st_facebook_large' displayText='Facebook'></span>
                <span class='st_twitter_large' displayText='Tweet'></span>
                <span class='st_linkedin_large' displayText='LinkedIn'></span>
                <span class='st_pinterest_large' displayText='Pinterest'></span>
                <span class='st_email_large' displayText='Email'></span>
            </div>
        </div>
    </div>
</div>
<script>
    $(".ndorse-now-top").click(function () {
        $(document).find("#selected_user_id").val("");
        $(".endorse-now-popupmodel button[type=submit]").click();
    });
</script>
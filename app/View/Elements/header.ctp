<?php
$authUser = AuthComponent::user();
//pr($data['video_feature']); exit;
?>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid header-bg-nav">
        <div class=" headerinfo">
            <div> 
                <div class="pull-left menu">
                    <div class="title-org">
                        <span style="margin-top:20px; font-size:16px;"><?php echo $data['textcenter']; ?></span>
                    </div>
                </div>

            </div>
            <div class="clearfix visible-xs"></div>
            <div class="RightTabs">

                <?php
//                pr($data); exit;
                if ($data['righttabs'] == 3) {
                    ?>
                    <!-- SUPER ADMIN Menu -->
                    <?php if (isset($data['video_feature']) && $data['video_feature'] == 1) { ?>
                        <span><a href="javascript:void(0)"><?php echo $this->Html->image('f-video-white.png', array('class' => '', 'url' => array("controller" => "users", "action" => "featurevideosetting", $data["orgid"]))); ?></a></span>
                    <?php } //allow_customer_portal ?>
                    <?php if (isset($data['daisy_portal']) && $data['daisy_portal'] == 1) { ?>
                        <span><a href="javascript:void(0)"><?php echo $this->Html->image('daisyportal.png', array('class' => '', 'url' => array("controller" => "users", "action" => "daisyportalsetting", $data["orgid"]))); ?> </a></span>
                    <?php } ?>

                    <?php if (isset($data['customer_portal']) && $data['customer_portal'] == 1) { ?>
                        <span><a href="javascript:void(0)"><?php echo $this->Html->image('feedback.png', array('class' => '', 'url' => array("controller" => "users", "action" => "customerportalsetting", $data["orgid"]))); ?></a></span>
                    <?php } ?>

                    <span><a href="javascript:void(0)"><?php echo $this->Html->image('live_endros.png', array('class' => '', 'url' => array("controller" => "organizations", "action" => "liveendorsement", $data["orgid"]))); ?></a></span>
                    <!-- OLD REPORT LINK -- >
    <!--<span ><a href="javascript:void(0)"><?php //echo $this->Html->image('reports_charts.png', array('class' => '', 'url' => array("controller" => "organizations", "action" => "reportsandcharts", $data["orgid"])));     ?></a></span>-->
                    <span ><a href="javascript:void(0)"><?php echo $this->Html->image('reports_charts.png', array('class' => '', 'url' => array("controller" => "organizations", "action" => "orgreportoverall", $data["orgid"]))); ?></a></span>
                <?php } ?>
                <span id="refresh"><a href="javascript:void(0)"><?php echo $this->Html->image('refresh.png', array('class' => '', 'width' => '36')); ?></a></span>

            </div>
        </div>
    </div>
</nav>
<div class="container-fluid">
    <div class="super-admin">
        <div class="col-sm-3 col-md-2 sidebar">
            <div id="wrapper">

                <ul class="sidebar-nav">
                    <li class="sidebar-brand">
                        <?php echo $this->Html->link('Organizations', array('controller' => 'organizations', 'action' => 'index')); ?>   
                    </li>
                    <!--                    <li class="sidebar-brand">-->
                    <?php //echo $this->Html->link('Organization Owners',array('controller'=>'users','action'=>'index'));   ?>      
                    <!--                    </li>-->
                    <li class="sidebar-brand">
                        <?php echo $this->Html->link('Set Up New Organization', array('controller' => 'users', 'action' => 'createorg')); ?>   
                    </li>
                    <?php
                    if ($authUser["role"] == "1") {
                        echo '<li class="sidebar-brand">' . $this->Html->link("Global Settings", array("controller" => "users", "action" => "setting")) . '</li>';
                        echo '<li class="sidebar-brand">' . $this->Html->link("Add Super Admin", array("controller" => "users", "action" => "addSuperAdmin")) . '</li>';
                        echo '<li class="sidebar-brand">' . $this->Html->link("Reset Password", array("controller" => "users", "action" => "resetpassword")) . '</li>';
                        echo '<li class="sidebar-brand">' . $this->Html->link("Stats", array("controller" => "stats", "action" => "index")) . '</li>';
                        echo '<li class="sidebar-brand">' . $this->Html->link("Reports", array("controller" => "reports", "action" => "index")) . '</li>';
                        echo '<li class="sidebar-brand">' . $this->Html->link("Pending Announcements", array("controller" => "users", "action" => "pendingannouncement")) . '</li>';
                        echo '<li class="sidebar-brand">' . $this->Html->link("Search Users", array("controller" => "users", "action" => "adminsearch")) . '</li>';
//                        echo '<li class="sidebar-brand">'.$this->Html->link("Guest Feedback Portal", array("controller" => "Customerportal",  "action" => "setting")).'</li>';
                    }
                    ?>
                    <?php if ($authUser["role"] == "2") { ?>
                        <li class="sidebar-brand"><?php echo $this->Html->Link("FAQS", array('controller' => 'users', 'action' => 'usersfaq')); ?></li>
                    <?php } ?>
                </ul>
                <div class="logout"> 
                    <span class="userName">
                        <?php echo $authUser['fname'] . ' ' . $authUser['lname']; ?>
                        <?php if ($authUser["role"] == "1") { ?>
                            <div class="clearfix"></div>
                            <span class="u-email" ><?php echo $authUser['email']; ?></span>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <?php echo $this->Html->link('LOGOUT', array('controller' => 'users', 'action' => 'logout'), array('class' => 'logoutText')); ?>
                    </span>
                    <?php if ($authUser["role"] == "2") { ?>
                        <span class="">
                            <?php echo $this->Html->Image("user.png", array("alt" => "user", "class" => "img-circle", 'width' => '61', 'height' => '61')); ?>
                        </span>
                    <?php } ?>
                </div>

            </div>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main col-xs-12">


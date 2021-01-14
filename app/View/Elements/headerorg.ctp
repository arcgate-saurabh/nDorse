<?php
$authUser = AuthComponent::user();
if (!isset($data)) {
    $data = array();
    $data['righttabs'] = 3;
    $data['orgid'] = $organizationId;
    $headerTitle = isset($headerTitle) ? $headerTitle : "";
} else {
    $headerTitle = $data['textcenter'];
}
?>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid header-bg">
        <div class="row headerinfo">
            <div> 
                <div class="pull-left menu">
                    <div class="title-org">
                        <span><?php echo $headerTitle; ?></span>
                    </div>
                </div>
            </div>
            <div class="clearfix visible-xs"></div>
            <div class="RightTabs">

                <?php if ($data['righttabs'] == 3) { ?>
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
                <span id="web-app">
                    <?php
                    //    echo $this->Html->link('admin.png', array('controller' => 'endorse'));
                    ?>
                    <a href="javascript:void(0);"><?php echo $this->Html->Image("web.png", array('id' => 'refresh', 'url' => array("controller" => "endorse"), 'width' => '75', 'alt' => 'Web App')); ?></a>

                </span> 
            </div>

        </div>
    </div>
</nav>
<div class="container-fluid">

    <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
            <div id="wrapper">

                <ul class="sidebar-nav">
                    <li class="sidebar-brand"><?php echo $this->Html->link('Organizations', array('controller' => 'organizations', 'action' => 'index')); ?></li>
                    <?php if ($authUser["role"] != "6") { ?>
                        <li class="sidebar-brand"><?php echo $this->Html->link('Create New Organization', array('controller' => 'users', 'action' => 'createorg', 'client_id' => $authUser["id"])); ?></li>
                    <?php } ?>
                    <li class="sidebar-brand"><?php echo $this->Html->link('My Profile', array('controller' => 'users', 'action' => 'editclient', $authUser["id"])); ?></li>
                    <?php if ($authUser["role"] != "1") { ?>
                        <li class="sidebar-brand"><?php echo $this->Html->Link("Announcements", array('controller' => 'organizations', 'action' => 'announcements')); ?></li>

                        <li class="sidebar-brand"><?php echo $this->Html->Link("Pending Announcements", array('controller' => 'organizations', 'action' => 'pendingannouncement')); ?></li>
                        <li class="sidebar-brand"><?php echo $this->Html->Link("FAQ", array('controller' => 'users', 'action' => 'usersfaq')); ?></li>
                    <?php } ?>
                </ul>
                <div class="logout" style=""> 
                    <span class="pull-left userName">
                        <?php echo ucfirst($authUser['fname']) . ' ' . ucfirst($authUser['lname']); ?>
                        <?php if ($authUser["role"] == "1") { ?>
                            <div class="clearfix"></div>
                            <span class="u-email" ><?php echo $authUser['email']; ?></span>
                        <?php } ?>
                        <div class="clearfix"></div>
                        <?php echo $this->Html->link('LOGOUT ', array('controller' => 'users', 'action' => 'logout'), array('class' => 'logoutText')); ?>
                    </span>
                    <?php if ($authUser["role"] == "2") { ?>
                        <span class="pull-right">
                            <?php
                            if (!empty($authUser["image"])) {
                                $filepath = WWW_ROOT . PROFILE_IMAGE_DIR . $authUser["image"];
                                if (file_exists($filepath)) {
                                    $user_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . "small/" . $authUser["image"];
                                    echo $this->Html->image($user_imagenew, array('width' => '70', 'height' => '70', "class" => "img-circle"));
                                } else {
                                    echo $this->Html->image("user.png", array("class" => "img-circle", 'width' => '61', 'height' => '61'));
                                }
                            } else {
                                echo $this->Html->image("user.png", array("class" => "img-circle", 'width' => '61', 'height' => '61'));
                            }
                            ?>

                        </span> 
                    <?php } ?>
                </div>

            </div>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">


<?php
$loggeinUser = AuthComponent::user();
?>

<div class="col-md-2 col-xs-6">
    <?php
    if (!empty($orgdetails["image"])) {
        $filepath = WWW_ROOT . ORG_IMAGE_DIR . $orgdetails["image"];

        //=============showing a image even in a case if it doesnt exist;
        if (file_exists($filepath)) {
            $imageinfo = getimagesize($filepath);
            $width = $imageinfo[0];
            $height = $imageinfo[1];
            $height = ($height >= 116) ? 116 : $height;
            $width = ($width >= 150) ? 150 : $width;
            $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR . $orgdetails["image"];
            echo $this->Html->image($org_imagenew, array('width' => $width, 'height' => $height, 'id' => 'org_image', 'class' => 'img-responsive'));
            //echo $this->Html->image($data['Organization']['health_url'],array('class'=>"img-responsive smiley", "width" => "40"));
        } else {
            echo $this->Html->image('img1.png', array('class' => "img-responsive", 'width' => '150'));
            //echo $this->Html->image($data['Organization']['health_url'],array('class'=>"img-responsive smiley", "width" => "40"));
        }
    } else {
        echo $this->Html->image('img1.png', array('class' => "img-responsive", 'width' => '150'));
        //echo $this->Html->image($data['Organization']['health_url'],array('class'=>"img-responsive smiley", "width" => "40"));
    }
    ?>
</div>
<div class="col-md-3 comp-name col-xs-6">
    <?php
    if ($page == "info" && $orgdetails['status'] != 2) {
        echo '<h2>' . $this->Html->link($orgdetails["name"], array('controller' => 'organizations', 'action' => 'info', $orgdetails["id"]));
    } else {
        echo '<h2>' . $this->Html->link($orgdetails["name"], array('controller' => 'organizations', 'action' => 'info', $orgdetails["id"]), array("target" => '_blank')) . '</h2>';
    }
    ?>
    <h3><?php echo $orgdetails["sname"]; ?></h3>
    <?php if (isset($orgdetails["secret_code"]) && $loggeinUser['role'] == 1) { ?>
                                                                        <!--<p> <strong>Organization Code: <?php echo $orgdetails["secret_code"]; ?></strong></p>-->
        <?php
    }
    if ($authUser["role"] == 1 && $page == "index") {
        foreach ($ownersarray[$orgdetails["id"]] as $orgownerid => $orgownername) {
            //echo '<div class="owner-name">' . $this->Html->link($orgownername, array("controller" => "users", "action" => "clientinfo", $orgownerid)) . '</div>';
        }
    }
    //==setting company address
    echo '<p>' . $orgdetails["street"];
    echo ($orgdetails["city"] != "" && $orgdetails["street"] != "") ? ", " : " ";
    echo $orgdetails["city"] . '</p>';

    echo '<p>' . $orgdetails["state"];
    echo ($orgdetails["state"] != "" && isset($orgdetails["country"]) && $orgdetails["country"] != "") ? ", " : " ";
    echo $orgdetails["country"] . '</p>';
    echo "<p>" . $orgdetails["zip"] . "</p>";
    ?>
</div>

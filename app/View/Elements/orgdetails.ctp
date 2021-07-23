<?php
$loggeinUser = AuthComponent::user();
?>
<div class="col-md-5 col-xs-6">
    <div class="row">
        <div class="col-md-6 col-xs-6">
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

            if ($page == "info") {
                if ($loggeinUser['role'] != 6) {
                    ?>
                    <div class="join-code">
                        <button class="btn btn-xs btn-success" onclick="generateJoinCode(<?php echo $orgdetails['id']; ?>);">Generate One Time Join Code</button>
                    </div>
                <?php }
                ?>
                <?php if ($authUser["role"] == 1) { ?>
                    <div class="themeEdit">
                        <span>Color Branding :</span> <?php echo ($orgdetails['theme'] == 1) ? '' : 'Custom'; ?> 
                        <span>
                            <?php echo $this->Html->Image("edit_icon.png", array("data-toggle" => "tooltip", "title" => "Color Branding", "url" => array('controller' => 'organizations', 'action' => 'colorsettings', $orgdetails["id"]))); ?>
                        </span>
                    </div>
                    <?php if ($orgdetails['id'] == 148 || $orgdetails['id'] == 415 || $orgdetails['id'] == 425 || $orgdetails['id'] == 446) { ?>
                        <div class="themeEdit">
                            <span>AD Settings :</span> <?php echo ($orgdetails['theme'] == 1) ? '' : 'Custom'; ?> 
                            <span>
                                <?php echo $this->Html->Image("edit_icon.png", array("data-toggle" => "tooltip", "title" => "AD Settting", "url" => array('controller' => 'organizations', 'action' => 'adsettings', $orgdetails["id"]))); ?>
                            </span>
                        </div>
                    <?php } ?>
                <?php } ?>
            <?php }
            ?>
        </div>
        <div class="col-md-6 comp-name col-xs-6">
            <?php
            if ($page == "info" && $orgdetails['status'] != 2) {
                //echo '<h2>' . $this->Html->link($orgdetails["name"], array('controller' => 'users', 'action' => 'editorg', $orgdetails["id"]), array("target" => '_blank'));

                if ($loggeinUser['role'] != 6) {
                    echo '<h2>' . $this->Html->link($orgdetails["name"], array('controller' => 'users', 'action' => 'editorg', $orgdetails["id"]));
                    echo $this->Html->Image("edit_icon.png", array("data-toggle" => "tooltip", "title" => "Edit Organization", "class" => "editorgimage", "url" => array('controller' => 'users', 'action' => 'editorg', $orgdetails["id"]))) . '</h2>';
                } else {
                    echo '<h2>' . $orgdetails["name"];
                }
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

    </div>
    <?php if ($page == "info" && $orgdetails['status'] != 2) { ?>
        <div class="row">
            <div class="col-xs-12">
                <div class="comp-name">
                    <?php
                    $shortCode = isset($orgdetails['short_code']) ? '' : $orgdetails['sname'];
                    ?>
                    <br/>
                    <?php if ($orgdetails['id'] == 148 || $orgdetails['id'] == 415 || $orgdetails['id'] == 425 || $orgdetails['id'] == 446) { ?>
                        <p>Short Code : <?php echo $shortCode; ?> </p>
                    <?php } ?>
                    <?php if ($authUser["role"] == 1 && ($orgdetails['id'] == 148 || $orgdetails['id'] == 415 || $orgdetails['id'] == 425 || $orgdetails['id'] == 446)) {
                        ?>
                        <p class="tooltip1">
                            Org SSO link :  <span id="portal_link"><?php echo Router::url('/', true, true) . 'sso/' . $shortCode; ?></span>
                            <a onclick="myFunction()" onmouseout="outFunc()" class="btn btn-default btn-sm ml15"  style="word-wrap: break-word;">
                                <span class="tooltiptext" id="myTooltip" style="word-wrap: break-word;">Copy to clipboard</span>
                                Copy Link
                            </a>
                        </p>
                        <br/>
                        <p class="tooltip1">
                            SSO Import link :  <span id="import_link"><?php echo Router::url('/', true, true) . 'organizations/bulkimportadfs/' . $orgdetails['id']; ?></span>
                            <a onclick="myFunction1()" onmouseout="outFunc()" class="btn btn-default btn-sm ml15"  style="word-wrap: break-word;">
                                <span class="tooltiptext" id="myTooltip1" style="word-wrap: break-word;">Copy to clipboard</span>
                                Copy Link
                            </a>
                        </p>
                        <br/>
                        <p class="tooltip2">
                            EmployeeID update link :  <span id="update_link"><?php echo Router::url('/', true, true) . 'organizations/bulkempupdate/' . $orgdetails['id']; ?></span>
                            <a onclick="myFunction2()" onmouseout="outFunc()" class="btn btn-default btn-sm ml15"  style="word-wrap: break-word;">
                                <span class="tooltiptext" id="myTooltip2" style="word-wrap: break-word;"></span>
                                Copy Link
                            </a>
                        </p>
                    <?php } ?>    
                </div>
            </div>
        </div>
    <?php } ?>    
    <script>
        function myFunction() {
            var $temp = $("<input>");
            $("body").append($temp);
            var myCode = $('#portal_link').html();
            $temp.val(myCode).select();
            document.execCommand("copy");
            $temp.remove();
            var tooltip = document.getElementById("myTooltip");
            //tooltip.innerHTML = "Link Copied: " + myCode;
            tooltip.innerHTML = "Link Copied";
        }
        function myFunction1() {
            var $temp = $("<input>");
            $("body").append($temp);
            var myCode = $('#import_link').html();
            $temp.val(myCode).select();
            document.execCommand("copy");
            $temp.remove();
            var tooltip = document.getElementById("myTooltip1");
            //tooltip.innerHTML = "Link Copied: " + myCode;
            tooltip.innerHTML = "Link Copied";
        }
        function myFunction2() {
            var $temp = $("<input>");
            $("body").append($temp);
            var myCode = $('#update_link').html();
            $temp.val(myCode).select();
            document.execCommand("copy");
            $temp.remove();
            var tooltip = document.getElementById("myTooltip2");
            //tooltip.innerHTML = "Link Copied: " + myCode;
            tooltip.innerHTML = "Link Copied";
        }
    </script>
</div>

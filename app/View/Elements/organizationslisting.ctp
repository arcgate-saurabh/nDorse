<?php
$ajaxurl = Router::url(array('controller' => 'ajax', 'action' => 'changeorgstatus'));

$subscriptionConfig = Configure::read("subscription");
//pr($orgdata);die;
foreach ($orgdata as $data) {
    //===============taking detail one by one and send this to orgdetail page
    $orgdetails = array(
        "id" => $data['Organization']['id'],
        "image" => $data['Organization']['image'],
        "name" => $data['Organization']['name'],
        "sname" => $data['Organization']['short_name'],
        "street" => $data['Organization']['street'],
        "city" => $data['Organization']['city'],
        "state" => $data['Organization']['state'],
        "zip" => $data['Organization']['zip'],
        "country" => $data['Organization']['country'],
    );
    ?>
    <div id="row_<?php echo $data['Organization']['id']; ?>" class="row row-padding <?php echo ($data['Organization']['status']) ? "" : "inactive"; ?>">
    <!--<div id="row_<?php //echo $data['Organization']['id'];         ?>" class="row lady-lake <?php echo ($data['Organization']['status']) ? "" : "inactive"; ?>"> -->
        <?php
        echo $this->Element("orgdetails", array('orgdetails' => $orgdetails, 'page' => 'index'));
        $org_image = $data['Organization']['image'];
        $orgname = $data['Organization']['name'];
        $id = $data['Organization']['id'];
        $encodedId = urlencode($ViewContFunctions->encodeString($data['Organization']['id']));
        ?>

        <div class="visible-xs hrone clearfix"></div>
        <div class="col-md-4 org-status col-xs-6">
            <?php
            if ($data['Organization']['status'] == 1) {
                $subscription_dispaly = "block";
            } else {
                $subscription_dispaly = "none";
            }
            ?>
            <div id="purchase_<?php echo $id; ?>" style="display:<?php echo $subscription_dispaly; ?>" >
                <?php
                $available_quota = 10;
                $stype = "normal";
                /*
                if ($authUser["role"] == 1) {
                    // print_r($data["Transactions"]);
                    
                    
                    
                    if( $data["Subscription"]["organization_id"] != $id) {
                        ?>
                        <button class="btn btn-xs btn-info" onclick="purchasesubscription('<?php echo $id; ?>')">Sell Subscription</button>
                        <?php
                    } else {
                        $available_quota+=$data["Subscription"]["pool_purchased"];
                        if ($data["Subscription"]["payment_method"] == "ndorse" && $data["Subscription"]["type"] == "paid" && $data["Subscription"]["is_deleted"] == 0) {
                            //  $available_quota+=$data["Subscription"]["pool_purchased"];
                            $stype = "ndorse";
                            ?>
                            <button class="btn btn-xs btn-info" onclick="upgradesubscription('<?php echo $id; ?>')">Upgrade</button>
                            <button class="btn btn-xs btn-info"  onclick="downgradesubscription('<?php echo $id; ?>', '<?php echo ($data["Subscription"]["pool_purchased"]); ?>')">Downgrade</button>
                            <button class="btn btn-xs btn-danger" onclick="terminatesubscription('<?php echo $id; ?>')" >Terminate Subscription</button>
                            <?php
                        } elseif ($data["Subscription"]["payment_method"] == "ndorse" & $data["Subscription"]["type"] == "paid" && $data["Subscription"]["is_deleted"] == 1) {
                            $stype = "ndorse";
                            ?>
                            <button class="btn btn-xs btn-danger" onclick="revertsubscription('<?php echo $id; ?>', '<?php echo $data["Subscription"]["pool_purchased"]; ?>')" >Revert Subscription</button>
                            <?php
                        } elseif ($data["Subscription"]["payment_method"] == "ndorse" && $data["Subscription"]["type"] == "trial") {
                            $stype = "ndorse";
                            ?>
                            <button class="btn btn-xs btn-info" onclick="upgradeSubscriptionTrial('<?php echo $id; ?>')" >Upgrade</button>
                            <button class="btn btn-xs btn-info" onclick="convertToPaidManual('<?php echo $id; ?>', '<?php echo $data["Subscription"]["pool_purchased"]; ?>', '<?php echo ($data["Subscription"]["pool_purchased"] * $subscriptionConfig['annual_price_per_user']); ?>')" >Convert to paid</button>
                            <button class="btn btn-xs btn-danger" onclick="terminatesubscriptiontrial('<?php echo $id; ?>')" >Terminate Subscription</button>

                            <?php
                        } elseif ($data["Subscription"]["payment_method"] == "web" && $data["Subscription"]["is_deleted"] == 0) {
                            $stype = "web";
                            ?>
                            <button class="btn btn-xs btn-info " og="<?php echo $encodedId; ?>" org_id="<?php echo $id; ?>" onclick="overwritesubscription('<?php echo $encodedId; ?>');" >Override Subscription</button>
                            <button class="btn btn-xs btn-danger" onclick="terminatesubscriptionbybraintree('<?php echo $encodedId; ?>')" >Terminate Subscription</button>
                            <?php
                        } elseif ($data["Subscription"]["payment_method"] == "web" && $data["Subscription"]["is_deleted"] == 1) {
                            // $available_quota+=$data["Subscription"]["pool_purchased"];
                            //  print_r($data);
                            $deletedby = "admin";
                            if (!empty($data["Transactions"]) && $data["Transactions"][0]["user_id"] == 1) {
                                $deletedby = "nDorse";
                            } elseif (!empty($data["Transactions"])) {
                                if ($data["Transactions"][0]["user_id"] == 0) {
                                    $deletedby = "admin (payment failed) ";
                                } else {

                                    //$deletedby = "admin (" . $ownersarray[$id][$data["Transactions"][0]["user_id"]] . ")";
                                    $deletedby = "admin (" . $adminusrarray[$data["Transactions"][0]["user_id"]] . ")";
                                }
                            }
                            ?>

                            <div class="msg">Subscription canceled by <?php echo $deletedby; ?></div>         
                        <?php } ?>

                    <?php } ?>
                    <?php
                } else {
                    if ($authUser["role"] != 6) {
                        ?>
                        <div id="js_orgAction_<?php echo $id; ?>" og="<?php echo $encodedId; ?>" >
                            <?php
                            if ($data["Subscription"]['is_deleted'] == 1 && $data["Subscription"]["type"] == "paid") {
                                $available_quota+=$data["Subscription"]["pool_purchased"];
                                $deletedby = "admin";
                                if (!empty($data["Transactions"]) && $data["Transactions"][0]["user_id"] == 1) {
                                    $deletedby = "nDorse";
                                } elseif (!empty($data["Transactions"])) {
                                    if ($data["Transactions"][0]["user_id"] == 0) {
                                        $deletedby = "admin (payment failed) ";
                                    } else {
                                        $deletedby = "admin (" . $adminusrarray[$data["Transactions"][0]["user_id"]] . ")";
                                    }
                                }
                                echo '<div class="msg">Subscription canceled by ' . $deletedby . '</div>';
                            } else {
                                if ($data["Subscription"]['payment_method'] != "ndorse") {
                                    if ($data["Subscription"]["organization_id"] == "" || $data["Subscription"]['is_deleted'] == 1) {
                                        ?>

                                        <?php echo $this->Html->link("Purchase Subscription", array('controller' => 'subscription', 'action' => 'btpurchase', $encodedId), array('class' => 'btn btn-info')); ?>
                                        <?php
                                    } else {
                                        $stype = "web";
                                        $available_quota+=$data["Subscription"]["pool_purchased"];
                                        ?>
                                        <button class="btn btn-xs btn-info js_updateSubscription" act="upgrade" og="<?php echo $encodedId; ?>">Upgrade</button>
                                        <button class="btn btn-xs btn-info js_updateSubscription" act="downgrade" og="<?php echo $encodedId; ?>" pp="<?php echo $data["Subscription"]['pool_purchased']; ?>">Downgrade</button>
                                        <button class="btn btn-xs btn-danger js_cancelSubscription" og="<?php echo $encodedId; ?>">Cancel Subscription</button>
                                        <?php
                                    }
//                                } else {
//                                            if($data["Subscription"]['is_deleted'] == 1) {
//                                                 $stype ="web";
//                                                $available_quota+=$data["Subscription"]["pool_purchased"];
//                                               
//                                   $deletedby ="admin";
//                                   if(!empty($data["Transactions"]) && $data["Transactions"][0]["user_id"]==1 )
//                                   {
//                                    $deletedby ="nDorse";
//                                    }elseif(!empty($data["Transactions"])){
//                                    
//                                     //$deletedby ="admin (".$ownersarray[$id][$data["Transactions"][0]["user_id"]].")";
//                                     if($data["Transactions"][0]["user_id"]==0){
//                                      $deletedby ="admin (payment failed) ";               
//                                    }else{
//                                       $deletedby ="admin (".$ownersarray[$id][$data["Transactions"][0]["user_id"]].")";         
//                                    }
//                                   }
//                                                
//                                                echo '<div class="msg">Subscription canceled by '.$deletedby.'</div>';
//                                            }
//                                }
                                } else {
                                    $stype = "ndorse";
                                    $available_quota+=$data["Subscription"]["pool_purchased"];
                                    echo '<div class="msg">' . 'Subscription purchased through nDorce LLC. Please contact  <a href="mailto:support@nDorse.net" _target="blank">nDorse support</a> if you want to update/cancel your subscription.' . '</div>';
                                }
                            }
                            ?>   </div>
                        <?php
                    }
                }
                
                */
                ?>

            </div> 

            <div id="orgstatus_<?php echo $data['Organization']['id']; ?>" type="<?php echo $stype; ?>">
                <h3>Organization Status: <?php echo ($data['Organization']['status'] == 1) ? "Active" : (($data['Organization']['status'] == 0) ? "Inactive" : "Deleted"); ?></h3>
            </div>
            <?php $target_id = $data['Organization']['id']; ?>
            <table>
                <tr>
                    <td>Available Limit:</td>
                    <td><div id="available_quota_<?php echo $data['Organization']['id']; ?>"> <?php echo $available_quota; ?></div></td>
                </tr>
                <tr>
                    <td>Total Users:</td>
                    <td><?php echo $totalusers[$data['Organization']['id']]; ?></td>
                </tr>
                <tr>
                    <td>Invitation Sent:</td>
                    <td><?php echo $inviationStats[$target_id]['total_invitations']["app"] + $inviationStats[$target_id]['total_invitations']["web"]; ?></td>
                </tr>
                <?php /* ?>
                  <tr>
                  <td>Via Mobile App:</td>
                  <td><?php echo $invitation_pending[$target_id]["app"];?></td>
                  </tr>
                  <tr>
                  <td>Via website:</td>
                  <td><?php echo $invitation_pending[$target_id]["web"];?></td>
                  </tr>
                  <?php */ ?>
                <tr>
                    <td>Invitation Accepted:</td>
                    <td><?php echo $inviationStats[$target_id]['invitations_accepted']; ?></td>
                </tr>
            </table>
        </div>
        <div class="col-md-3 col-xs-6">
            <div class="pull-right">
                <div class="col-md-6 col-sm-12" >
                    <?php if ($authUser["role"] != 6) { ?>
                        <a href="javascript:void(0);" rel="<?php echo $data['Organization']['id']; ?>_one" class="dots">
                            <?php echo $this->Html->image('3dots.png', array('align' => 'pull-right')); ?>
                        </a>
                    <?php } ?>
                    <div class="arrow_box <?php echo $data['Organization']['id']; ?>_one" style="position:absolute; right:-18px;z-index:2;">
                        <div style="border:0px solid #f00; margin-top:-35px; margin-right:5px;" class="pull-right">
                            <?php
                            echo $this->Html->image('popupArrow.png');
                            echo $data['Organization']['status'];
                            ?>
                        </div>
                        <ul>
                            <?php if ($data['Organization']['status'] != 2) { ?>
                                <li id="statuschanges_<?php echo $data['Organization']['id']; ?>"><a href="#" data-toggle="modal" onclick="changestatus(<?php echo $id; ?>, '<?php echo $ajaxurl; ?>', '<?php echo $data['Organization']['status']; ?>', 'Organization')"><?php echo ($data['Organization']['status']) ? "Inactivate" : "Activate"; ?></a></li>
                                <li><a href="javascript:void(0)" onclick="deleteorganizations(<?php echo $data['Organization']['id']; ?>)" >Delete</a></li>
                            <?php } ?>
                            <li>
                                <?php echo $this->Html->link("Reports and Charts", array("controller" => "organizations", "action" => "reportsandcharts", $data['Organization']['id'])); ?>
                            </li>
                            <li>
                                <?php echo $this->Html->link("Live Endorsements", array("controller" => "organizations", "action" => "liveendorsement", $data['Organization']['id'])); ?>
                            </li>
                        </ul>
                    </div>
                    <?php echo $this->Element('deleteitem', array('data' => $data)); ?>
                </div>

            </div>
            <?php //echo $this->Element("endorsementcounter", array("endorsementformonth" => $endorsementformonth[$target_id])); ?>
        </div>

    </div>

<?php } ?>
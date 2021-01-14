
<div class="col-md-5 user-subcribed">
                                    <?php //print_r($orgdata['Subscription']); ?>
                                    <table class="table">
                                        <tr>
                                            <td>Users subscribed:</td>
                                        </tr>
                                        <tr>
                                            <td>Subscription Start Date:</td>
                                        </tr>
                                        <tr>
<!--                                            <td>Payment Amount:</td>-->
                                        </tr>
                                        <tr>
                                            <td>Payment Method Used:</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-4 user-subcribed">
                                    <table class="table">
                                        <tr>
                                            <td class="js_poolPurchased"><?php echo $subscription["pool_purchased"];?></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo date("m/d/Y",strtotime($subscription["start_date"]));?></td>
                                        </tr>
                                        <tr>
<!--                                            <td>$<?php echo number_format($subscription["amount_paid"], 2, '.', '');;?></td>-->
                                        </tr>
                                        <tr>
                                            <td>
                                            <?php
                                            if($subscription['payment_method']=="web"){
                                              echo  "Online Payment Gateway";
                                            }elseif($subscription['payment_method']=="ndorse" && $subscription['type']=="paid"){
                                             echo "nDorse Payment System";
                                            }elseif($subscription['payment_method']=="ndorse" && $subscription['type']=="trial"){
                                             echo "Free Trial";
                                            }
                                            ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-3">
                                    <div class="endrosd-month">
                                        <p>Subscription End Date: </p>
                                        <h3>
                                        <?php  //if($subscription['payment_method']=="web"){
                                            echo date("m/d/Y",strtotime($subscription["end_date"]));
                                       // }else{
                                       //echo date("m/d/Y",strtotime($subscription["end_date"].'-1 day'));
                                        //}
                                        ?></h3>
                                    </div>
                                </div>
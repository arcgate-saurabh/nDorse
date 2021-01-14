<?php

$data = array(
            "textcenter" => "Organization Info",
            "righttabs" => "1",
            "orgid" => $organization_id
        );
        $headerpage = ($authUser["role"]==1) ? 'header' : 'headerorg';
        if($authUser["role"]== 2){
		$data['auth_users'] = $authUser;
	}
        echo $this->Element($headerpage, array('data' => $data));
    ?>

<div class="row row-padding" id="usersdetail">
    <div class="col-md-4">
    <?php
        $user_image = $userdata['User']['image'];
        if($user_image==""){
            echo '<span class="pull-left" style="margin-right:15px;">'.$this->Html->image('user.png',array('class'=>"img-circle",  'width'=>'61','height'=>'61')).'</span>'; 
        }else{
            if(file_exists(WWW_ROOT. PROFILE_IMAGE_DIR  .$user_image)){
                $user_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR  .$user_image;
                echo '<span class="pull-left" style="margin-right:15px;">'.$this->Html->image($user_imagenew,array("width"=>"61","height"=>"61","id"=>"org_image","class"=>"img-circle")).'</span>';

            }else {
                $user_imagenew = $this->Html->image('user.png',array('class'=>"img-circle", 'width'=>'61','height'=>'61')); 
                echo '<span class="pull-left" style="margin-right:15px;">'.$user_imagenew.'</span>';
            }
        }
    ?>
        <h6 class="user"><?php echo ucfirst($userdata['User']['fname'])." ". ucfirst($userdata['User']['lname']);?></h6>
        <h5 class="user-mail"><?php echo $userdata['User']['email']; ?></h5>
    </div>
    <div class="col-md-2 comp-name">
    <?php
        $orgname = $companydetail['name'];
        $orgid = $organization_id;
        echo '<h2>'.$this->Html->link($orgname,array('controller'=>'users','action'=>'editorg',$orgid)); 
        //echo $this->Html->Image("edit_icon.png", array("data-toggle" => "tooltip", "title" => "Edit Organization", "class" => "editorgimage", "url" => array('controller'=>'users','action'=>'editorg',$orgid))).'</h2>';
        echo '<h3>'.$companydetail['shortname'].'</h3>'
    ?>
        <p><?php echo $companydetail["street"]; if($companydetail["street"]!="" && $companydetail["city"]!=""){echo ", ";}?> <?php echo $companydetail["city"];?></p>
        <p><?php echo $companydetail["state"];if($companydetail["state"]!="" && $companydetail["zip"]!=""){echo ", ";}?> <?php echo $companydetail["zip"];?></p>
    </div>
</div>
<section>
<div class="row"> 
    <button type="button" id="printFullList" class="btn btn-info btn-Preview-Image" rel="complete">Print all nDorsements</button>
</div>
</section>
<section>
    <div class="row">
        <div class="col-md-12" id="listingreportser">
            <div class="row">
                <div class="col-md-8">
                    <h3 style="color:#fff;">nDorsement Received: </h3>
                </div>
                <div class="col-md-4">
                    <div class="container-fluid"> 
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav navbar-right">
                <?php /*<li><a href="#"><img src="<?php echo $this->webroot; ?>img/search_map-white.png" alt="" /></a></li> */?>
                                <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $this->webroot; ?>img/pancake-white.png" alt="" /> </a>
                                    <ul class="dropdown-menu">
                  <!--                    <li><a href="#"><?php // echo $this->Html->link(__('Save as Spreadsheet'), array('controller' => 'organizations', 'action' => 'export', '?' => array('orgid' => $organization_id, 'userid' => $user_id, 'information' => 'endorser')));?></a></li>-->
                                        <li><a href="javascript:void(0)" id="endorsementsreceivedsas" class="endorsementssas" data-userid = "<?php echo $user_id;?>" data-information = "endorser">Save As Spreadsheet</a></li>
                                        <li><a href="javascript:void(0)" rel="listingreportser" class="btn-Preview-Image">Print</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <!-- /.navbar-collapse --> 
                    </div>
                </div>
            </div>
            <div data-example-id="striped-table" class="row bs-example">
                <div class="table-responsive scroll-header">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><div class="col-endor">nDorsed</div></th>
                                <th><div class="endor-date">nDorsement Date</div></th>
                                <th style="text-align: center"><div class="endor-date">Core Values</div></th>
                                <?php 
                                    foreach($allothervalues["corevalues"] as $key =>$corevaluesall){
                                        echo '<th style="text-align:center;" class="iffyTip1" title="'.$corevaluesall["name"].'">'.$corevaluesall["name"].'</th>';
                                    }
                                ?>
                                <th><div class="comment-div">Comments</div></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                      if(empty($allvaluesendorser)){?>
                            <tr>
                                <td colspan="5">No Data Available</td>
                            </tr>
                        <?php }else{
                                    foreach($allvaluesendorser as $endorservalues){?>
                            <tr>
                                <td ><?php echo $endorservalues["name"];?></td>
                                <td><?php echo $this->Time->format($endorservalues["date"], DATEFORMAT);?></td>
                                <td style="text-align: center"><?php echo $endorservalues["totalpoints"];?></td>
                                <?php
                                    foreach($allothervalues["corevalues"] as $key =>$corevaluesall){
                                        if(in_array($key, $endorservalues["corevaluesid"])){
                                            echo '<td style="text-align: center">'.$this->Html->Image("checked.png", array("alt" => "Checked")).'</td>';
                                        }else{
                                            echo '<td style="text-align: center"></td>';
                                        }
                                    }
                                ?>
                                <td ><?php echo $endorservalues["endorsement_message"];?></td>
                            </tr>
                                <?php }
                                } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <br>
    <br>
    <div class="row">
        <div class="col-md-12" id="listingreportseg">
            <div class="row">
                <div class="col-md-8">
                    <h3 style="color:#fff;">nDorsement Given:</h3>
                </div>
                <div class="col-md-4">
          <?php
//                                echo $this->Html->link(__('Export'), array('controller' => 'organizations', 'action' => 'export', '?' => array('orgid' => $organization_id, 'userid' => $user_id, 'information' => 'endorsed')));
//                                
//                                echo $this->Html->image('fullview.png',array('class'=>"img-responsive full-view", "width" => 22, "height" => 22));
//                                echo $this->Html->image('search_map.png',array('class'=>"img-responsive full-view", "width" => 22, "height" => 22));
                            ?>
                    <div class="container-fluid"> 
                        <!-- Collect the nav links, forms, and other content for toggling -->
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                            <ul class="nav navbar-nav navbar-right">
                <?php /*<li><a href="#"><img src="<?php echo $this->webroot; ?>img/search_map-white.png" alt="" /></a></li> */?>
                                <li class="dropdown"> <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="<?php echo $this->webroot; ?>img/pancake-white.png" alt="" /> </a>
                                    <ul class="dropdown-menu">
                  <!--                    <li><a href="#"><?php echo $this->Html->link(__('Save as Spreadsheet'), array('controller' => 'organizations', 'action' => 'export', '?' => array('orgid' => $organization_id, 'userid' => $user_id, 'information' => 'endorsed')));?></a></li>-->
                                        <li><a href="javascript:void(0)" id="endorsementsreceivedsas" class="endorsementssas" data-userid = "<?php echo $user_id;?>" data-information = "endorsed">Save As Spreadsheet</a></li>
                                        <li><a href="javascript:void(0)" rel="listingreportseg" class="btn-Preview-Image">Print</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                        <!-- /.navbar-collapse --> 
                    </div>
                </div>
            </div>
            <div data-example-id="striped-table" class="row bs-example">
                <div class="table-responsive scroll-header">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th><div class="col-endor">nDorser</div></th>
                                <th><div class="endor-date">nDorsement Date</div></th>
                                <th style="text-align:center;"><div class="endor-date">Core Values</div></th>
                <?php 

                                    foreach($allothervalues["corevalues"] as $key =>$corevaluesall){
                                        //$tmp[] = $key;
                                        echo '<th style="text-align:center;" class="iffyTip1" title="'.$corevaluesall["name"].'" >'.$corevaluesall["name"].'</th>';
                                    }
                                    ?>
                                <th style="text-align:center;"><div class="comment-div">Comments</div></th>
                            </tr>
                        </thead>
                        <tbody>
              <?php 
                                if(!empty($allvaluesendorsed)){
                                    
                                 foreach($allvaluesendorsed as $endorsedvalues){?>
                            <tr>
                                <td><?php echo $endorsedvalues["name"];?></td>
                                <td><?php echo $this->Time->format($endorsedvalues["date"],DATEFORMAT);?></td>
                                <td style="text-align:center;"><?php echo $endorsedvalues["totalpoints"];?></td>
                <?php
                                        foreach($allothervalues["corevalues"] as $key =>$corevaluesall){
                                            if(in_array($key, $endorsedvalues["corevaluesid"])){
                                                echo '<td style="text-align:center;">'.$this->Html->Image("checked.png", array("alt" => "Checked")).'</td>';
                                            }else{
                                                echo '<td></td>';
                                            }
                                        }
                                    ?>
                                <td ><?php echo $endorsedvalues["endorsed_message"];?></td>
                            </tr>
              <?php }
                                }else{?>
                            <tr>
                                <td colspan="5">No Data Available</td>
                            </tr>
              <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

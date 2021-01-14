<?php 
$ajaxurl = Router::url(array('controller' => 'ajax','action' => 'changeorgstatus'));
foreach($userdata as $data) {?>
<tr id="row_<?php echo $data['User']['id']; ?>" class="<?php echo ($data['User']['status']) ? "" : "inactive"?>">
    <td>
        <?php
        $user_image = $data['User']['image'];
        if($user_image==""){
        echo $this->Html->image('user.png',array('class'=>"img-circle")); 

        }else{
         $user_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR  .$user_image;

         //echo WWW_ROOT. PROFILE_IMAGE_DIR  .$user_image;

         if(file_exists(WWW_ROOT. PROFILE_IMAGE_DIR  .$user_image)){
         echo $this->Html->image($user_imagenew,array('width'=>'61','height'=>'61','id'=>'org_image','class'=>"img-circle"));
         } else {
          echo $this->Html->image('user.png',array('class'=>"img-circle")); 
         }
         }?>
    </td>
    <td><h6 style="font-size:18px; margin:2px;">
            <?php
            $username = $data['User']['fname'].' '.$data['User']['lname'];
            $userid = $data['User']['id'];
            echo $this->Html->link($username,array('controller'=>'users','action'=>'clientinfo',$userid)); ?>
        </h6>
        <p style="color:#c2c1c1; font-size:12px;">
                        <?php echo $data['User']['email']; ?>     
            <br>
            Last updated on: <?php echo $data['User']['updated']; ?><!--2015-08-16 22:43--><br>
            Created on: <?php echo $data['User']['created']; ?><!--2015-06-16 22:43--></p></td>
    <td>Free</td>
    <td>N/A</td>
    <td id="statusactivity_<?php echo $data['User']['id']; ?>" class="text-active">
                        <?php echo ($data['User']['status']) ? "Active" : "Inactive"?>
    </td>
    <td><?php echo $nooforg[$data['User']['id']];?></td>
    <td><?php echo $noofusers[$data['User']['id']];?></td>
    <td>
        <div class="ThreeDotsImg"><a href="javascript:void(0);" rel="<?php echo $data['User']['id']; ?>_one" class="dots">
                         <?php echo $this->Html->image('3dots.png'); ?>
            </a>
            <div class="arrow_box <?php echo $data['User']['id']; ?>_one">
                <div class="pull-right popupArrow">
                                    <?php echo $this->Html->Image("popupArrow.png");?>
                </div>
                <ul>
                    <li id="statuschanges_<?php echo $data['User']['id'];?>"><a href="#" onclick="changestatus(<?php echo $data['User']['id']; ?>, '<?php echo $ajaxurl; ?>', '<?php echo $data['User']['status']; ?>', 'User')" data-toggle="modal"><?php echo ($data['User']['status']) ? "Inactivate" : "Activate"; ?></a></li><!--data-target="#myModal"-->
<!--                    <li><a href="#"  data-toggle="modal" data-target="#myModa2_<?php //echo $data['User']['id']; ?>">Delete</a></li>-->
                    <li><a href="javascript:void(0)" onclick="deleteuser(<?php echo $data['User']['id']; ?>)">Delete</a></li>
                </ul>
            </div>
        </div>
    </td>
</tr>
<?php } ?>
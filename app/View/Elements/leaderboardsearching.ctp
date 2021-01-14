<?php
foreach($arrayendorsementdetail as $endorsementdetail) {?>
<tr>
    <?php if(!empty($endorsementdetail["userid"])){?>
    <?php //if($endorsementdetail["endorser"] == 0 && $endorsementdetail["endorsed"] == 0){?>
<!--    <td width="18%" style="text-align:left;"><?php //echo $endorsementdetail["name"];?></td>-->
                  <?php //}else{ ?>
    <td><?php echo $this->Html->Link($endorsementdetail["name"], array("controller" => "organizations", "action" => "listingreports", $endorsementdetail["userid"]))?></td>
                  <?php //}?>
    <td style="text-align: center;"><?php echo $endorsementdetail["endorser"];?></td>
    <td style="text-align: center;"><?php echo $endorsementdetail["endorsed"];?></td>
    <td style="text-align: center;"><?php echo $endorsementdetail["endorsed"] + $endorsementdetail["endorser"];?></td>
    <td><?php echo $endorsementdetail["department"];?></td>
    <td><?php echo $endorsementdetail["entity"];?></td>
    <td><?php echo $endorsementdetail["title"];?></td>
    <td><?php echo $endorsementdetail["subcenter_name"];?></td>
    <?php }?>
</tr>
<?php } ?>
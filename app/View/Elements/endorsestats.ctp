<?php $nodresegivenvalue = ceil($statesdata["endorse_given"]/10);
	  $ndorsereceivedvalue = $statesdata["endorse_received"];
	  $tolalvalue = $nodresegivenvalue+$ndorsereceivedvalue%100;?>
<div class="ndorse-states">
        <table class="table table-hover table-states">
          <tr>
            <th>nDorsement Given </th>
            <th class="text-right"><?php echo $statesdata["endorse_given"];?></th>
          </tr>
          <tr>
            <th>nDorsement Recieved </th>
            <th class="text-right"><?php echo $statesdata["endorse_received"];?></th>
          </tr>
        </table>
      </div>
      <div class="core-values-div">
		<?php if(!empty($statesdata["core_value"])){ ?>
        <table class="table table-hover table-core-value">
          <tr>
            <th colspan="2"><strong>Core Values Collected </strong></th>
          </tr>
		  <?php foreach($statesdata["core_value"] as $coreval){?>
          <tr>
            <td><?php echo $coreval["name"];?> </td>
            <td class="text-right"><?php echo $coreval["value"];?> 
              </t>
          </tr>
		  <?php } ?>
         
        </table>
		<?php } ?>
      </div>=====<?php if(!empty($statesdata["badges"])){?>
            <div class="col-md-9">
				<?php foreach($statesdata["badges"] as $badgesval){?>
				<div class="badge-count text-center"><img  alt="" id="client_image" src="<?php echo  $badgesval["image"];?>" width="60"><br />
                <?php echo  $badgesval["count"];?> </div>
              <?php } ?>
              
            </div>
			<?php } ?>=====<?php echo $tolalvalue;?>
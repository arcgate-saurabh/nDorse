<?php $ajaxurl = Router::url(array('controller' => 'ajax','action' => 'deleteorgstatus')); ?>
<div class="modal fade" id="myModa2_<?php echo $data['Organization']['id'];?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content" align="center">
			<div class="modal-header">
				<button type="button" class="btn btn-default pull-right close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
				<!-- <h4 class="modal-title" id="gridSystemModalLabel">Modal title</h4>--> 
			</div>
		<div class="modal-body">
			<h4 class="modal-title">ARE YOU SURE YOU WANT TO DELETE?</h4>
			<p>This will delete all data of the organization</p>
		</div>
		<div class="modal-footer">
			<div class="text-center"><button onclick="deleteitem(<?php echo $data['Organization']['id'];?>, '<?php echo $ajaxurl;?>')" type="button" class="btn btn-primary btn-blue">Yes</button>
			<button type="button" class="canceldelete btn btn-primary btn-blue">No</button></div>
		</div>
		</div>
	</div>
</div>
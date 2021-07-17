<?php echo $this->element("/client/myorg",array("orgdata"=>$orgdata)); ?>

<!-- <div class="congratulation">
  	<div class=" col-md-12 text-center">
      	<h2>
	       Currently, you have not been assigned an Organization. Please create, join or switch to an Organization. Go to MENU to "Create and/or Join An Org" and then switch to an Organization to set your default Organization.
	    </h2>
    </div>
</div> -->
<?php if(isset($alertMsg)) { ?>
<script type="text/javascript">
    alertbootbox("<?php echo $alertMsg; ?>");
</script>
<?php } ?>

<?php echo $this->element("/client/search_organization",array("orgdata"=>$join_orgdata)); ?>

<div class="forgot-password">
<?php echo $this->Form->create('Forgot', array('class' => '', 'id' => "recoverUsernameForm")); ?>
<div class="form-group" >
<span>Enter the email in the box below to recover username</span>
<input type="text" class="form-control" placeholder="Recovery Email" name="email"/>
</div>
<div class="form-group">
    <button class="btn  btn-orange btn-block" type="submit">Submit </button>
</div>
<?php echo $this->Form->end();?>
</div>
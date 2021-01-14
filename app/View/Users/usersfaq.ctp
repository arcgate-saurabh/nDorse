<?php
    if(!empty($authUser)){
        $data = array(
            "textcenter" => "FAQ",
            "righttabs" => "1",
            "auth_users" => $authUser
        );
        echo $this->Element('headerorg', array('data' => $data));
    }else{?>

<div class="container-fluid header-bg">
  <div class = "row">
    <div class="col-sm-12 col-md-12"> <?php echo $this->Html->link($this->Html->image('backtologin.png',array('alt'=>'img', 'class' => 'pull-left')),array("controller" => "Users", "action" => "login"), array('escape'=>false)); ?>
      <p class="text-center" style="margin:5px;">FAQ</p>
    </div>
  </div>
</div>
<div class="container-fluid">
<div class = "row">
<div class="col-sm-12 col-md-12 main">
<?php } ?>

<!-- Page Content -->
<?php
if(empty($authUser)){?>
<div class="row">
  <div class="col-md-6">
    <div class="panel panel-default">
      <div class="panel-heading">Contact us</div>
      <div class="panel-body">
        <form id="faqform" onsubmit="return false" class="form-horizontal">
          <div class="form-group form-group-lg">
            <div class="col-sm-12"> <?php echo $this->Form->Input("Name", array("class" => "form-control", "placeholder" => "Name", "label" => false));?> </div>
          </div>
          <div class="form-group form-group-lg">
            <div class="col-sm-12"> <?php echo $this->Form->Input("Email", array("class" => "form-control", "placeholder" => "Email", "label" => false));?> </div>
          </div>
          <div class="form-group form-group-lg">
            <div class="col-sm-12"> <?php echo $this->Form->Input("Subject", array("class" => "form-control", "placeholder" => "Subject", "label" => false));?> </div>
          </div>
          <div class="form-group form-group-lg">
            <div class="col-sm-12"> <?php echo $this->Form->textarea("Message", array("class" => "form-control", "placeholder" => "Message", "label" => false));?> </div>
          </div>
          <button id="faqbutton" class="btn btn-primary btn-send">Send</button>
        </form>
      </div>
    </div>
  </div>
  <?php }
  if(!empty($authUser)){
      echo '<div class="col-md-12">';
  }else{
      echo '<div class="col-md-6">';
  }
  ?>
    <div class="panel panel-default">
      <div class="panel-heading">FAQ</div>
      <div class="panel-body" style="min-height:397px;" ><?php echo $this->Element("faqelement");?></div>
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="col-md-12 MT30" >
    <div class="contact-detial">
      <div class="text-center">
        Website: www.ndorse.net<br />
        Email: <?php echo SUPPORTEMAIL;?>
      </div>
       </div>
       <div class="text-center"><?php echo $this->Html->link($this->Html->image('google_play.png',array('alt'=>'img', 'class' => 'google-play')),'https://play.google.com/store/apps/details?id=net.susco.ndorse', array('target'=>'_blank','escape'=>false)); ?> <?php echo $this->Html->link($this->Html->image('App_store.png',array('alt'=>'img', 'class' => 'google-play')),'https://itunes.apple.com/us/app/ndorse-enterprise/id985005314?ls=1&mt=8', array('target'=>'_blank','escape'=>false)); ?></div>
  </div>
</div>
<?php echo $this->Element("commonpopupmessage");?> 
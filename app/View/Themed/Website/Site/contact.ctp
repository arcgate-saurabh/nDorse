<?php
    echo $this->Html->css('font-awesome'); 
?>
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container">
  <div class="download-ndorse"> <?php echo $this->Html->Image("/images/logo.png", array("width" => "100", "align" => "left"));?>
    <div class="pull-left">
      <h2>CONTACT US</h2>
    </div>
  </div>
</div>
<section class="get-app">
  <div class="container">
    <div class="col-md-6">
      <div class="android-block">
        <div class="pull-right">
          <div class="android-icon">
              <?php echo $this->Html->link($this->Html->image('/images/android-icon.png',array('alt'=>'')),'https://play.google.com/store/apps/details?id=net.susco.ndorse&utm_source=global_co&utm_medium=prtnr&utm_content=Mar2515&utm_campaign=PartBadge&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1', array('target'=>'_blank','escape'=>false)); ?> 
              <?php //echo $this->Html->Image("/images/android-icon.png", array("alt" => "", 'class' => 'img-responsive'));?>
          </div>
        </div>
        <div class="pull-right">
            <?php echo $this->Html->link($this->Html->image('/images/android.png',array('alt'=>'')),'https://play.google.com/store/apps/details?id=net.susco.ndorse&utm_source=global_co&utm_medium=prtnr&utm_content=Mar2515&utm_campaign=PartBadge&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1', array('target'=>'_blank','escape'=>false)); ?> 
            <?php //echo $this->Html->Image("/images/android.png", array("alt" => "", 'class' => 'img-responsive'));?> 
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="iPhone-block">
        <div class="pull-left">
          <div class="iPhone-icon">
              <?php echo $this->Html->link($this->Html->image('/images/iPhone-icon.png',array('alt'=>'')),'https://itunes.apple.com/us/app/ndorse-enterprise/id985005314?ls=1&mt=8', array('target'=>'_blank','escape'=>false)); ?> 
              <?php //echo $this->Html->Image("/images/iPhone-icon.png", array("alt" => "", 'class' => 'img-responsive'));?> </div>
        </div>
        <div class="pull-left">
            <?php echo $this->Html->link($this->Html->image('/images/iPhone.png',array('alt'=>'')),'https://itunes.apple.com/us/app/ndorse-enterprise/id985005314?ls=1&mt=8', array('target'=>'_blank','escape'=>false)); ?> 
            <?php //echo $this->Html->Image("/images/iPhone.png", array("alt" => "", 'class' => 'img-responsive'));?> 
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
  </div>
</section>
<section class="contact-us">
  <div class="container">
    <div class="col-md-12 text-center">
      <div class="col-md-8"> <?php echo $this->Form->Create("zdesk", array("class" => "form-horizontal", "return" => "false"));?> 
        <!--                <form class="form-horizontal" role="form">-->
        <div class="form-group">
          <label class="control-label col-sm-2" for="email">Contact Us:</label>
          <div class="col-sm-9 pull-right">
            <input type="text" name="uname" class="form-control"  placeholder="Name">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-2" for="pwd">www.ndorse.net</label>
          <div class="col-sm-9 pull-right">
            <input type="email" name="uemail" class="form-control" placeholder="Email">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-2" for="pwd2">support@ndorse.net</label>
          <div class="col-sm-9 pull-right">
            <input type="text" class="form-control" name="usubject" placeholder="Subject">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-2" for="pwd"></label>
          <div class="col-sm-9 pull-right">
            <textarea placeholder="Message" name="umessage" class="form-control"></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-12">
            <button type="button" id="contactussubmit" class="btn btn-green pull-right">Send</button>
          </div>
        </div>
        <?php echo $this->Form->end(); ?> </div>
      <div class="col-md-4"> <?php echo $this->Html->link($this->Html->image('/images/app-store.png',array('alt'=>'')),'https://itunes.apple.com/us/app/ndorse-enterprise/id985005314?ls=1&mt=8', array('target'=>'_blank','escape'=>false)); ?> <?php echo $this->Html->link($this->Html->image('/images/google-play.png',array('alt'=>'')),'https://play.google.com/store/apps/details?id=net.susco.ndorse&utm_source=global_co&utm_medium=prtnr&utm_content=Mar2515&utm_campaign=PartBadge&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1', array('target'=>'_blank','escape'=>false)); ?> </div>
    </div>
  </div>
</section>
<div class="col-md-12 text-center"><?php echo $this->Html->link("Terms & Conditions", array("controller" => "site", "action" => "tnc"));?> | <?php echo $this->Html->link("Privacy Policy", array("controller" => "site", "action" => "privacypolicy"));?></div>
<br>
<br>
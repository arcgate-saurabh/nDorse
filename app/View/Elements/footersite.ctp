<br>
<section class="contact-us">
  <div class="container">
    <div class="col-md-12 text-center">
      <div class="col-md-8 col-lg-3"> <?php echo $this->Html->Image("/images/Contact-Us.png", array("width" => "224", "height" => "60", "alt" => "", "url" => array("controller" => "site", "action" => "contact")));?> </div>
      <div class="col-md-4 col-lg-offset-5"> 
            <?php echo $this->Html->link($this->Html->image('/images/app-store.png',array('alt'=>'')),'https://itunes.apple.com/us/app/ndorse-enterprise/id985005314?ls=1&mt=8', array('target'=>'_blank','escape'=>false)); ?> 
            <?php echo $this->Html->link($this->Html->image('/images/google-play.png',array('alt'=>'')),'https://play.google.com/store/apps/details?id=net.susco.ndorse&utm_source=global_co&utm_medium=prtnr&utm_content=Mar2515&utm_campaign=PartBadge&pcampaignid=MKT-Other-global-all-co-prtnr-py-PartBadge-Mar2515-1', array('target'=>'_blank','escape'=>false)); ?> </div>
    </div>
  </div>
</section>
<div class="col-md-12 text-center"><?php echo $this->Html->link("Terms & Conditions", array("controller" => "site", "action" => "tnc"));?> | <?php echo $this->Html->link("Privacy Policy", array("controller" => "site", "action" => "privacypolicy"));?></div>
<br><br>

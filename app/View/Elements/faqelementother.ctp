<?php //print_r($faq);?>

<div class="faq-accordion-mobile"><div id="" class="panel-group" <?php if(isset($style)){ echo $style;} ?> >
  <?php foreach($faq as $val){
               
                $faqdata = $val["globalsettingFaq"];
                
                ?>
  <div id="<?php echo $faqdata["id"];?>" class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title"> <a href="#collapse<?php echo $faqdata["id"];?>" data-parent="#accordion" data-toggle="collapse" class="accordion-toggle collapsed"><?php echo $faqdata["question"];?> </a> </h4>
    </div>
    <div class="panel-collapse collapse" id="collapse<?php echo $faqdata["id"];?>">
      <div class="panel-body"> <?php echo $faqdata["answer"];?> </div>
    </div>
  </div>
  <?php } ?>
</div></div>

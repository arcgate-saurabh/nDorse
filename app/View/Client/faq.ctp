<?php
if($layout == "ajax"){
    echo $data; 
}else{?>

<div class="col-md-12 row">
  <div class="panel panel-default">
    <div class="panel-heading">FAQ</div>
    <div class="panel-body" style="min-height:397px;" >
      <div id="accordion faqbody" class="panel-group">
        <?php 
            //=converting object to array
            $array = json_decode(json_encode($data), true);
            
            foreach($array as $dataforfaq){
                $datafaq = $dataforfaq["globalsettingFaq"];?>
        
        <!--FAQs re: nDorse App-->
        <div id="panelid<?php echo $datafaq["id"];?>" class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title"> <a href="#collapse<?php echo $datafaq["id"];?>" data-parent="#accordion" data-toggle="collapse" class="accordion-toggle collapsed"> <?php echo $datafaq["question"];?> </a> </h4>
          </div>
          <div class="panel-collapse collapse" id="collapse<?php echo $datafaq["id"];?>">
            <div class="panel-body"> <?php echo $datafaq["answer"];?> </div>
          </div>
        </div>
        <?php }
            ?>
      </div>
    </div>
  </div>
</div>
<div class="clearfix"></div>
<?php 

}
?>

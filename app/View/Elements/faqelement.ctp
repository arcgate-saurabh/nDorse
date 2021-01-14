<div class="faq-accordion" <?php if(isset($style)){ echo $style;} ?>>
    <div class="panel-group" id="accordion faqbody">
<div class="faqHeader"></div>
<!--FAQs re: nDorse App-->
    <?php foreach($faqdata as $datafaq){?>
        <div class="panel panel-default" id="panelid<?php echo $datafaq["globalsettingFaq"]["id"];?>">
            <div class="panel-heading">
                <?php if($authUser["role"] == 1){?>
                    <div style="position:relative; right:-10px;">
                        <span class="glyphicon glyphicon-pencil pull-right editfaq" data-toggle="tooltip" data-original-title="Edit" data-idfaq ="<?php echo $datafaq["globalsettingFaq"]["id"];?>"></span> 
                        <span class="deletefaq pull-right glyphicon glyphicon-trash" data-toggle="tooltip" data-original-title="Delete" data-idfaq ="<?php echo $datafaq["globalsettingFaq"]["id"];?>"></span>
                    </div>
                <?php }?>

                <h4 class="panel-title"> <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $datafaq["globalsettingFaq"]["id"];?>" > <?php echo $datafaq["globalsettingFaq"]["question"];?> </a> </h4>
            </div>
            <div id="collapse<?php echo $datafaq["globalsettingFaq"]["id"];?>" class="panel-collapse collapse">
                <div class="panel-body"> <?php echo nl2br($datafaq["globalsettingFaq"]["answer"], false);?> </div>
            </div>
        </div>
    <?php }?>
    </div>
</div>

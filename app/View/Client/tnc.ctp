<?php 
    if($layout == "ajax"){
        echo $data; 
    }else{?>
        <div class="col-md-12">
    <div class="panel panel-default">
            <div class="panel-body"  >
                <?php echo $data; ?>
            </div>
    </div>
    <?php }
?>
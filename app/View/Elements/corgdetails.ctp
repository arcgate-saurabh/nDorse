<div class="col-md-12 bottom-bor">
    <h3><?php echo $orgdata["org_name"];?></h3>
    <?php if($orgdata["org_sname"]!=""){?><h4><?php echo $orgdata["org_sname"];?></h4><?php } ?>
    <?php if(!empty($orgdata["streetcity"])){?><h4><?php echo implode(", ", $orgdata["streetcity"]);?></h4><?php } ?>
    <?php if(!empty($orgdata["statecountry"])){?><h4><?php echo implode(", ", $orgdata["statecountry"]);?></h4><?php } ?>
    <?php if($orgdata["zip"]!=""){?><h4><?php echo $orgdata["zip"];?></h4><?php } ?>
</div>
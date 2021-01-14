<?php 
foreach($alldata as $newdata){
?>
<div class="top-nDorser">
    <h4><?php echo $newdata["title"];?> <?php echo (isset($newdata["duration"]) && $newdata["duration"]!="") ? "<span class='range'>".$newdata["duration"]."</span>":"";?></h4>
    <section class="bor-white">
        <?php foreach($newdata["list"] as $listingdata){
        //====if empty data comes
        if($listingdata["name"] == ""){?>
        <div class="no-nDorsements">
            <h4>No nDorsements data for this <?php echo ucfirst($listingdata["type"]);?> </h4>
        </div>
        <?php continue; }
        ?>
        <div class="media material-media">
            <div class="media-left">
                <?php 
                if ($listingdata["image"] != "") {
                    $file_headers = @get_headers($listingdata["image"]);
                    $image = ($file_headers[0] != 'HTTP/1.1 404 Not Found') ? $listingdata["image"]: Router::url('/', true)."img/user.png";
                    echo '<img src="'.$image.'" alt="avatar" class="media-object material-media__object">';
                } else {
                    echo $this->Html->image("user.png", array("class" => "media-object material-media__object", "alt" => "avatar"));
                }?>
            </div>
            <div class="media-body">
                <h4 class="media-heading material-media__title"><?php echo ucfirst($listingdata["fname"])." ".ucfirst($listingdata["lname"]); ?></h4>
            </div>
        </div>
        <?php }?>
    </section>
</div>
<?php 
}?>
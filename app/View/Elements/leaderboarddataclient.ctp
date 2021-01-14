<?php
foreach($alldata as $leaderboarddata){
    if(!empty($leaderboarddata["list"])){
    ?>
    <tbody>
        <tr class="table-head">
            <td><strong><?php 
            if(strtolower($leaderboarddata["title"])!="employee"){
                echo $leaderboarddata["title"];
            }
            ?> </strong></td>
            <td><strong><?php echo $leaderboardtype;?></strong></td>
        </tr>
        <?php foreach($leaderboarddata["list"] as $leaderboardlist){?>
        <tr>
            <td><?php echo ucwords($leaderboardlist["name"]);?>
            <?php if(strtolower($leaderboarddata["title"])=="employee" && $leaderboardlist["department"]!=""){
                echo '<br><span style="font-size:15px">'.$leaderboardlist["department"].'</span>';
            }?>
            </td>
            <td><?php echo $leaderboardlist["Total"];?></td>
        </tr>
        <?php }?>
    </tbody>
<?php } 
} ?>
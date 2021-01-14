<?php if (empty($allPostData)) { ?>
    <tr id="nodata"><td colspan="5">No Data Available</td></tr>
    <?php
} else {
//    pr($allPostData); exit;
    $counter = 1;
    foreach ($allPostData as $index => $postData) {
//        pr($postData);
        ?>
        <td style="width: 40%;">
            <?php echo remove_emoji($postData['Post']["title"]); ?>
        </td>

        <td style="text-align:center;width: 20%;" class="marks">
            <div class="col-endor marks" style="text-align:center;">
                <?php
                echo ($postData[0]["total_post_click"] > 0 ) ? $this->Html->Image("checked.png", array("alt" => "Checked")) : "-";
                ?>
            </div>
        </td>
        <td style="text-align:center;width: 20%;" class="marks">
            <div class="col-endor marks" style="text-align:center;">
                <?php
                echo ($postData[0]["total_attachment_pin_click"] > 0 ) ? $this->Html->Image("checked.png", array("alt" => "Checked")): "-";
                ?>
            </div>
        </td>
        <td style="text-align:center;width: 20%;" class="marks">
            <div class="col-endor marks" style="text-align:center;">
                <?php
                echo ($postData[0]["total_attachment_click"] > 0 ) ? $this->Html->Image("checked.png", array("alt" => "Checked")): "-";
                ?>
            </div>
        </td>
<!--        <td style="text-align:center;width: 20%;">
            <?php // echo $postData['PostEventCount']["created"]; ?>
        </td>-->
        </tr>
        <?php
        $counter++;
    }
}

function remove_emoji($text) {
    $text = preg_replace('/\\\\u[0-9A-F]{4}/i', '', $text);
    return $text;
}

?>
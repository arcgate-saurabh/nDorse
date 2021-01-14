<?php if (empty($allPostData)) { ?>
    <tr id="nodata"><td colspan="5">No Data Available</td></tr>
    <?php
} else {
//    pr($allPostData); 
    $counter = 1;
    foreach ($allPostData as $index => $postData) {
        ?>
        <?php if (isset($filtered)) { ?>
            <tr username = '<?php echo $postData[0]["user_name"];?>'>
            <?php } else { ?>
            <tr username = '<?php echo $postData[0]["user_name"];?>'>
            <?php } ?>
            <td>
                <?php echo $postData[0]["user_name"]; ?>
            </td>
            <td>
                <?php echo $postData['OrgJobTitle']["title"]; ?>
            </td>
            <td>
                <?php echo $postData['OrgDepartment']["department_name"]; ?>
            </td>
            <td>
                <?php echo $postData['Entity']["sub_org_name"] != '' ? $postData['Entity']["sub_org_name"] : '-'; ?>
            </td>
            <td style="text-align:center">
                <?php echo $postData[0]["total_post_click"]; ?>
            </td>
            <td style="text-align:center">
                <?php echo $postData[0]["total_attachment_pin_click"]; ?>
            </td>
            <td style="text-align:center">
                <?php echo $postData[0]["total_attachment_click"]; ?>
            </td>
            <td style="text-align:center">
                <?php echo ($postData[0]["total_attachment_click"]+$postData[0]["total_attachment_pin_click"]); ?>
            </td>
            <td style="text-align:center">
                <?php echo $postData[0]["total_post_like"]; ?>
            </td>

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
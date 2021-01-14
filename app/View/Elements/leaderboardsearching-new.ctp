<?php
foreach ($subCenterArray as $subcenterID => $subcenterName) {
//    pr($subcenterNdorsementArray);
    ?>
    <tr id="subcenter_<?php echo $subcenterID; ?>" data-id="<?php echo $subcenterID; ?>" class="subcenter_row">
        <td width="16%" style="text-align:left;">
            <?php echo $subcenterName; ?>
        </td>
        <td width="14%" align="center">
            <?php
            if (isset($subcenterNdorsementArray[$subcenterID]['given'])) {
                echo $subcenterNdorsementArray[$subcenterID]['given'];
            } else {
                echo '0';
            }
            ?>
        </td>
        <td width="16%" align="center">
            <?php
            if (isset($subcenterNdorsementArray[$subcenterID]['received'])) {
                echo $subcenterNdorsementArray[$subcenterID]['received'];
            } else {
                echo '0';
            }
            ?>
        </td>

    </tr>
<?php } ?>
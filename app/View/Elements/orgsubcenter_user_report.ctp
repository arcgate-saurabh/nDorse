<?php foreach ($orgAllUserDataArray as $userID => $userData) {
    ?>
    <tr id="subcenter_user_<?php echo $userID; ?>" data-id="<?php echo $userID; ?>" class="subcenteruser_row subcenteruser_<?php echo $userData['subcenter_id']; ?> subcenteruser_<?php echo $userData['subcenter_id']; ?>_<?php echo $userData['dept_id']; ?> ">

        <td width="20%" class="headerSortDown botBrdr">
            <?php
            echo $this->Html->link($userData['name'], array('controller' => 'organizations', 'action' => 'userreport', $organization_id, $userID), array('target' => '_blank'));
            ?>
        </td>

        <td width="20%" class="headerSortDown botBrdr">
            <?php
            if (isset($deptIDArray[$userData['dept_id']])) {
                echo $deptIDArray[$userData['dept_id']];
            } else {
                "";
            }
            ?>
        </td>
        <td width="20%" class="headerSortDown botBrdr">
            <?php
            if (isset($subcenterIdArray[$userData['subcenter_id']])) {
                echo $subcenterIdArray[$userData['subcenter_id']];
            } else {
                "";
            }
            ?>
        </td>
        <td width="15%" class="headerSortDown botBrdr">
            <?php
            if (isset($jobTitleIdArray[$userData['jobtitle_id']])) {
                echo $jobTitleIdArray[$userData['jobtitle_id']];
            } else {
                "";
            }
            ?>
        </td>
        <td width="20%" class="headerSortDown botBrdr">
            <?php
            if (isset($usersNdorsementsCounts[$userID]['sent'])) {
                echo $usersNdorsementsCounts[$userID]['sent'];
            } else {
                echo "0";
            }
            ?>
        </td> 
        <td width="15%" class="headerSortDown botBrdr">
            <?php
            if (isset($usersNdorsementsCounts[$userID]['received'])) {
                echo $usersNdorsementsCounts[$userID]['received'];
            } else {
                echo "0";
            }
            ?>
        </td>
    </tr>
<?php } ?>


<?php
//pr($subcenterDepartmentArray);
//exit;
$deptSubcenterArray = array();
$subcenterName = array();
foreach ($subcenterDepartmentArray as $subDeptId => $subCenterArray) {
    $deptSubcenterArray[$subCenterArray['dept_id']] = $subCenterArray['subcenter_id'];
    $subcenterName[$subCenterArray['subcenter_id']] = $subCenterArray['subcenter_name'];
}
//pr($deptSubcenterArray);
//pr($deptNdorsementCount);
//exit;
//pr($orgDepartments); //exit;
//foreach ($subcenterDepartmentArray as $subcenterDeptID => $subcenterDeptData) {
foreach ($orgDepartments as $orgDeptID => $orgDeptName) {
    $subcenter_id = 0;
    $subCname = '';
    $classname = "subcenterdept_row_" . $subcenter_id;
    if (isset($deptSubcenterArray[$orgDeptID])) {
        if (isset($deptSubcenterArray[$orgDeptID])) {
            $subcenter_id = $deptSubcenterArray[$orgDeptID];
        }
        if (isset($subcenterName[$subcenter_id])) {
            $subCname = $subcenterName[$subcenter_id];
        }
    }
    ?>
    <!--<tr id="subcenter_dept_0" data-id="<?php //echo $orgDeptID;     ?>" class="subcenterdept_row subcenterdept_row_<?php //echo $subcenterDeptData['subcenter_id'];     ?>" data-subcenter="<?php // echo $subcenterDeptData['subcenter_id'];     ?>">-->

                    <!--<tr id="subcenter_dept_0" data-id="<?php // echo $orgDeptID;     ?>" class="subcenterdept_row subcenterdept_row_<?php //echo $subcenter_id;     ?>" data-subcenter="<?php //echo $subcenter_id;     ?>">-->
    <tr id="subcenter_dept_0" data-id="<?php echo $orgDeptID; ?>" class="subcenterdept_row <?php echo $classname; ?>" data-subcenter="<?php echo $subcenter_id; ?>">
        <td width="30%" style="text-align:left;">
    <?php echo $orgDeptName; ?>
        </td>
        <td width="30%" style="text-align:left;">
    <?php echo $subCname; ?>
            <?php echo ""; ?>
        </td>
        <td width="20%" align="center">
    <?php
    if (isset($deptNdorsementCount[$orgDeptID]['sent'])) {
        echo $deptNdorsementCount[$orgDeptID]['sent'];
    } else {
        echo "0";
    }
    ?>

        </td>
        <td width="20%" align="center">
    <?php
    if (isset($deptNdorsementCount[$orgDeptID]['received'])) {
        echo $deptNdorsementCount[$orgDeptID]['received'];
    } else {
        echo "0";
    }
    ?>
        </td>
    </tr>
<?php } ?>
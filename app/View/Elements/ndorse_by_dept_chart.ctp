<?php 
foreach ($subcenterDepartmentArray as $subcenterDeptID => $subcenterDeptData) { ?>
    <tr id="subcenter_dept_<?php echo $subcenterDeptID; ?>" data-id="<?php echo $subcenterDeptID; ?>" class="subcenterdept_row">
        <td width="30%" style="text-align:left;">
            <?php echo $subcenterDeptData['dept_name']; ?>
        </td>
        <td width="30%" style="text-align:left;">
            <?php echo $subcenterDeptData['subcenter_name']; ?>
        </td>
        <td width="20%" align="center">0</td>
        <td width="20%" align="center">0</td>
    </tr>
<?php } ?>
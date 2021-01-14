<?php
$searchedDepartments = array();
$searchedDepartments = $searchResult;
$selectedDepartments = isset($endorseSelected['department']) ? $endorseSelected['department'] : array();
$ifEmpty = true;
?>
<div class="suggestion dept-suggestion">
    <div class="form-group main-width">
        <?php
//        pr($searchedDepartments);
        foreach ($searchedDepartments as $record) {
            if (!in_array($record['id'], $selectedDepartments)) {
                ?>
                <?php
                $ifEmpty = false;
                $searchClass = "";
                $lockIcon = "";
                $searchClass = "js_searched";
                ?>
                <div class="select-dept" data-id="<?php echo $record['id']; ?>" data-type="department">
                    <h2><?php echo $record['name']; ?></h2>
                    <?php echo $this->Form->input('department_id', array('id' => 'endorse_department_id', 'name' => 'endorse_department_id', 'type' => 'hidden', 'value' => $record['id'])); ?>
                </div>
                <?php
            }
        }
        ?>
        <?php
        if ($ifEmpty) {
            echo '<div class="no-data-search no-data"><h3>No Data available</h3></div>';
        }
        ?>
    </div>
</div>
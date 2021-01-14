<?php

$searchedDepartments = $searchResult->departments;
$searchedUsers = $searchResult->users;
$searchedEntities = $searchResult->entities;

$selectedDepartments = isset($endorseSelected['department']) ? $endorseSelected['department'] : array();
$selectedUsers = isset($endorseSelected['user']) ? $endorseSelected['user'] : array();
$selectedEntities = isset($endorseSelected['entity']) ? $endorseSelected['entity'] : array();
$ifEmpty = true;
?>


<?php
foreach ($searchedUsers as $record) {
    if (!in_array($record->id, $selectedUsers)) {  
        $ifEmpty = false;
        $searchClass = "";
        $lockIcon = "";
        if ($record->endorse_count >= $endorsementLimit) {
            $lockIcon = '<span class="locked"><img src="' . Router::url('/', true) . 'img/locked.png" class=""></span>';
            $searchClass = "js_noAdd";
        } else {
            $searchClass = "js_searched";
        }
        ?>
<div class="autoSearchValue searched-values <?php echo $searchClass; ?>" data-endorsementfor="user" data-endorsedid="<?php echo $record->id; ?>"> 
            <?php
                echo $this->form->input('firstname',array('id'=>'firstname','type'=>'hidden','value'=>$record->fname));
                echo $this->form->input('lastname',array('id'=>'lastname','type'=>'hidden','value'=>$record->lname));
                echo $this->form->input('department_id',array('id'=>'department_id','type'=>'hidden','value'=>$record->department_id));
                echo $this->form->input('department_name',array('id'=>'department_name','type'=>'hidden','value'=>$record->department_name));
            ?>
    <span class="nDorse-user-icon">
        <img src="<?php echo Router::url('/', true); ?>img/user-icon.png" class=""></span> 
    <span class="js_searchedName"><?php echo $record->name; ?></span>
            <?php echo $lockIcon; ?> 
</div>
        <?php
    }
}
foreach ($searchedDepartments as $record) {
    if (!in_array($record->id, $selectedDepartments)) {
        $ifEmpty = false;
        $searchClass = "";
        $lockIcon = "";
        if ($record->endorse_count >= $endorsementLimit) {
            $lockIcon = '<span class="locked"><img src="' . Router::url('/', true) . 'img/locked.png" class=""></span>';
        } else {
            $searchClass = "js_searched";
        }
        ?>
<div class="searched-values <?php echo $searchClass; ?>" data-endorsementfor="department" data-endorsedid="<?php echo $record->id; ?>"> 
    <span class="nDorse-user-icon">
        <img src="<?php echo Router::url('/', true); ?>img/pub-icon.png" class="">
    </span> 
    <span class="js_searchedName"><?php echo $record->name; ?></span>
            <?php echo $lockIcon; ?> 
</div>
        <?php
    }
}

foreach ($searchedEntities as $record) {
    if (!in_array($record->id, $selectedEntities)) {
        $ifEmpty = false;
        $searchClass = "";
        $lockIcon = "";
        if ($record->endorse_count >= $endorsementLimit) {
            $lockIcon = '<span class="locked"><img src="' . Router::url('/', true) . 'img/locked.png" class=""></span>';
        } else {
            $searchClass = "js_searched";
        }
        ?>
<div class="searched-values <?php echo $searchClass; ?>" data-endorsementfor="entity" data-endorsedid="<?php echo $record->id; ?>"> 
    <span class="nDorse-user-icon">
        <img src="<?php echo Router::url('/', true); ?>img/org-icon.png" class="">
    </span> 
    <span class="js_searchedName"><?php echo $record->name; ?> </span>
            <?php echo $lockIcon; ?> 
</div>
        <?php
    }
}

if(!$ifEmpty){ ?>
<div style="position: relative; padding: 7px; border-top: 1px solid #ccc; margin-top: 5px; text-align: right;" class="">
    <button class="btn btn-xs btn-warning hand js_closeSearch" type="button">CLOSE</button>
</div>
<?php }
//if ($ifEmpty) {
//    echo '<div class="no-data-search">No Data available</div>';
//}
?>

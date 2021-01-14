<?php
$searchedDepartments = $searchResult->departments;
$searchedUsers = $searchResult->users;
$searchedEntities = $searchResult->entities;

$selectedDepartments = isset($endorseSelected['department']) ? $endorseSelected['department'] : array();
$selectedUsers = isset($endorseSelected['user']) ? $endorseSelected['user'] : array();
$selectedEntities = isset($endorseSelected['entity']) ? $endorseSelected['entity'] : array();
$ifEmpty = true;
?>

<div style="position:absolute; right:10px; top:10px;z-index: 999" class="">
    <button class="btn btn-xs btn-warning hand js_closeSearch" type="button">CLOSE</button>
</div>
<?php
//pr($searchedUsers); exit;
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
        <div class="searched-values <?php echo $searchClass; ?>" data-endorsementfor="user" data-endorsedid="<?php echo $record->id; ?>" data-subcenterid="<?php echo $record->subcenter_id; ?>"> 
            <span class="nDorse-user-icon">
                <img src="<?php echo Router::url('/', true); ?>img/user-icon.png" class=""></span> 
            <span class="js_searchedName"><?php echo $record->name; ?></span> -  <span class="js_searchedName"><?php echo $record->email; ?></span> - <span class="js_searchedName"> Department: <?php echo $record->department; ?></span>  - <span class="js_searchedName subcentername"><?php echo $record->subcenter_long; ?></span>
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

if ($ifEmpty) {
    echo '<div class="no-data-search">No Data available</div>';
}
?>

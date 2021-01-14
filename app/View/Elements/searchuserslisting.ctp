<table id="mytable" class="table table-condensed table-hover tablesorter">
    <thead>
        <tr>
            <th class="header" id="role"></th>
            <th class="header" id="role"></th>
            <th class="header" id="role">Current Organization</th>
            <th id="role" class="header headerSortDown">Status <img src="/ndorse/prod2/img/down-arrow.png" class="statusdown" alt=""><img src="/ndorse/prod2/img/up-arrow.png" class="statusup" style="display:none" alt=""> </th>
            <th id="role" class="header">Role <img src="/ndorse/prod2/img/down-arrow.png" class="statusdown" alt=""><img src="/ndorse/prod2/img/up-arrow.png" class="statusup" style="display:none" alt=""></th>
            <th class="header" id="role"></th>
        </tr>
    </thead>
    <tbody id="userslisting">
        <?php
        $loggedinUser = AuthComponent::user();
        foreach ($org_user_data as $data) {
            $post_id[] = $data['UserOrganization']['id'];
            ?>
            <tr id="row_<?php echo $data['UserOrganization']['id']; ?>">
                <td>
                    <?php
                    $user_image = $data['User']['image'];
                    if ($user_image == "") {
                        echo $this->Html->image('user.png', array('class' => "img-circle", 'width' => '61', 'height' => '61'));
                    } else {
                        if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image)) {
                            $user_imagenew = Router::url('/', true) . "app/webroot/" . PROFILE_IMAGE_DIR . $user_image;
                            echo $this->Html->image($user_imagenew, array('width' => '61', 'height' => '61', 'id' => 'org_image', 'class' => "img-circle"));
                        } else {
                            echo $this->Html->image('user.png', array('class' => 'img-circle', 'width' => '61', 'height' => '61'));
                        }
                    }
                    ?>
                </td>
                <td>
                    <h6 style="color:#ffffff; font-size:18px; margin:2px; color:#337ab7">
                        <?php
                        $name = ucfirst($data['User']['fname']) . ' ' . ucfirst($data['User']['lname']);
                        echo '<div style="cursor:pointer" class="usersprofile" data-userorgid="' . $data['UserOrganization']['id'] . '">' . $name . '</div>';
                        ?>
                    </h6>
                    <p style="color:#c2c1c1; font-size:14px;"><?php echo $data['User']['email']; ?><br>
                        Last updated on: <?php
                        //echo $this->App->dateConvertDisplay($data['User']['updated']);
                        echo $this->Time->Format($data['User']['updated'], DATEFORMAT);
                        //echo $data['User']['updated']; 
                        ?><br>
                        Created on: <?php echo $this->Time->Format($data['User']['created'], DATEFORMAT); ?>
                    </p>
                </td>
                <td><?php echo $data['Organization']['name'];?></td>
                <td class="text-active"><?php echo ($data['UserOrganization']['status'] == 1) ? "Active" : (($data['UserOrganization']['status'] == 0) ? "Inactive" : "Evaluation Mode"); ?>
                <td id="roleendorser_<?php echo $data['UserOrganization']['user_id']; ?>"><?php echo ($data['UserOrganization']['user_role'] == 3) ? ENDORSER : DESIGNATEDADMIN; ?></td>
            </tr>

        <?php } ?>
    </tbody>
</table>
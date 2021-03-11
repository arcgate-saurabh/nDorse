<div class="col-md-12 profile">
    <div class="col-md-12">
            <div class="user-profile">
                <table class="table table-hover table-striped">
                    <tbody>
                        <?php
                        if (!empty($jsonNotificationDataArray)) {
                            foreach ($jsonNotificationDataArray as $index => $notificationDATA) {
//                                                pr($notificationDATA);
                                ?>
                                <tr>
                                    <td><?php echo $this->Html->image($notificationDATA['user_image'], array('class' => 'img-circle hand show-user-profile', 'width' => "50px", 'height' => '50px')); ?> <?php echo $notificationDATA['plain_msg']; ?></td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

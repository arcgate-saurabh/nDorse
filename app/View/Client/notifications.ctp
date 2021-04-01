<div class="col-md-12 profile">
    <div class="col-md-12">
        <div class="user-profile">
            <table class="table table-hover table-striped">
                <tbody>
                    <?php
                    if (!empty($jsonNotificationDataArray)) {
                        foreach ($jsonNotificationDataArray as $index => $notificationDATA) {
//                            pr($notificationDATA);
                            ?>

                            <tr>
                                <td>
                                    <?php
//                                    echo $notificationDATA['feed_type'];
                                    if ($notificationDATA['feed_type'] != 'null') {
                                        $rootUrl = Router::url('/', true);
                                        if ($notificationDATA['feed_type'] == 'ndorse') {
                                            $link = $rootUrl . "endorse/details/" . $notificationDATA['feed_id'];
                                        } else {
                                            $link = $rootUrl . "post/details/" . $notificationDATA['feed_id'];
                                        }
                                        ?>
                                        <a href="<?php echo $link; ?>" style="color: beige;text-decoration: none;">
                                            <?php
                                            echo $this->Html->image($notificationDATA['user_image'], array('class' => 'img-circle hand show-user-profile', 'width' => "50px", 'height' => '50px'));
                                            echo '<span style="margin-left: 20px;">' . $notificationDATA['plain_msg'] . "</span>";
                                            ?>
                                        </a>                                    
                                        <?php
                                    } else {
                                        echo $this->Html->image($notificationDATA['user_image'], array('class' => 'img-circle hand show-user-profile', 'width' => "50px", 'height' => '50px'));
                                        echo '<span style="margin-left: 20px;">' . $notificationDATA['plain_msg'] . "</span>";
                                    }
                                    ?>

                                </td>

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

<div class="">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Likes</h3>
            <a href="javascript:void(0);" class="CloseList"><i class="fa fa-times-circle closeModal"></i></a>
        </div>
        <form role="form" enctype="multipart/form-data">
            <div class="box-body">
                <?php
                if (!empty($likesList)) {
//                    pr($likesList);
                    foreach ($likesList as $index => $likeData) {
                        $userImage = $likeData['user_image'];
                        if (isset($userImage) && $userImage != '') {
                            if (strpos($userImage, 'localhost') < 0) {
                                $userImage = str_replace("https", "http", $userImage);
                            }
                            echo "<div class='listings'><span class='listingsPic'>" . $this->Html->image($userImage, array("width" => '50px')) . "</span>";
                        }

                        echo $likeData['username'] . "</div>";
                    }
                } else {
                    echo "<div class='no-data-nDorse' style='color:#211F1F;' >No one liked.</div>";
                }
                ?>
            </div>
        </form>
    </div>
</div>        

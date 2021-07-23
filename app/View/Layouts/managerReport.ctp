<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php //echo $cakeDescription  ?>
            <?php echo $this->fetch('title'); ?>
        </title>
        <?php if (isset($description) && $description != "") { ?>
            <meta name="description" content="<?php echo $description; ?>" />
        <?php } ?>
        <script>

            var siteurl = '<?php echo Router::url('/', true, true); ?>';



            if (siteurl.indexOf('localhost') > -1) {
                if (siteurl.indexOf('https') > -1) {
                    siteurl = siteurl.replace("http", "https");
                }
            } else if (siteurl.indexOf('ndorse.net') > -1) {
                if (siteurl.indexOf('https') > -1) {

                } else {
                    //siteurl = siteurl.replace("http", "https");
                }
            }

//            else {
//                siteurl = siteurl.replace("https", "http");
//            }
            var userprofile = '<?php echo Router::url('/', true); ?>setImage';
            //userprofile = userprofile.replace("http", "https");

            var imgurl = '<?php echo Router::url('/', true); ?>app/webroot/<?php echo PROFILE_IMAGE_DIR; ?>/';
            //imgurl = imgurl.replace("http", "https");

            var orguploadimage = '<?php echo Router::url('/', true); ?>setOrgImage';
            if (orguploadimage.indexOf('localhost') > 0) {
                orguploadimage = orguploadimage.replace("http", "https");
            }


            var orgimgurl = '<?php echo Router::url('/', true); ?>app/webroot/<?php echo ORG_IMAGE_DIR; ?>/';
            //orgimgurl = orgimgurl.replace("http", "https");

        </script>
        <?php
        echo $this->Html->css("bootstrap.min");
        echo $this->Html->css("signin");
        echo $this->Html->css("style");
        echo $this->Html->css("jquery-ui");
        echo $this->Html->css("simple-sidebar");
        echo $this->Html->css("select2");
        echo $this->Html->css("bootstrap-datetimepicker"); //added @7-dec-2017 by babulal prasad
        echo $this->Html->css("font-awesome.min"); //added @7-dec-2017 by babulal prasad
        echo $this->Html->css("jquery-confirm.min"); //added @16-jan-2018 by babulal prasad
        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        echo $this->Html->script('jquery.min');
        echo $this->Html->script('jquery.form');
        echo $this->Html->script('jquery.validate');
        echo $this->Html->script('custom');
        echo $this->Html->script('common');
        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('bootstrap');
        echo $this->Html->script('bootbox.min');
        echo $this->Html->script('ndorse');
        echo $this->Html->script('select2');
        echo $this->Html->script('jquery.tablesorter');
        echo $this->Html->script('moment'); //added @7-dec-2017 by babulal prasad
        echo $this->Html->script('bootstrap-datetimepicker'); //added @7-dec-2017 by babulal prasad
        echo $this->Html->script('jquery-confirm.min'); //added @16-jan-2018 by babulal prasad
        if (isset($jsIncludes)) {
            foreach ($jsIncludes as $jsincluded) {
                echo $this->Html->script($jsincluded);
            }
        }
        ?>
    </head>
    <body>
        <?php echo $this->fetch('content'); ?>
    </body>
</html>



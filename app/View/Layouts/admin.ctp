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
$cakeDescription = __d('cake_dev', 'CakePHP: the rapid development php framework');
$cakeVersion = __d('cake_dev', 'CakePHP %s', Configure::version())
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
	<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
        <script type='text/javascript'>
            var siteUrl = '<?php echo Router::url('/', true); ?>';

            if ((siteUrl.indexOf('localhost') > -1) || (siteUrl.indexOf('staging') > -1)) {
                if (siteurl.indexOf('https') > -1) {
                    siteUrl = siteUrl.replace("https", "http");
                }

            } else if (siteUrl.indexOf('ndorse.net') > -1) {
                if (siteUrl.indexOf('https') > -1) {

                } else {
                    siteUrl = siteUrl.replace("http", "https");
                }
            }


            //siteUrl = siteUrl.replace("http", "https");

            var userprofile = '<?php echo Router::url('/', true); ?>setImage';
            userprofile = userprofile.replace("http", "https");

            var orguploadimage = '<?php echo Router::url('/', true); ?>setOrgImage';
            orguploadimage = orguploadimage.replace("http", "https");
            //console.log(orguploadimage);
            var orgcpuploadimage = '<?php echo Router::url('/', true); ?>setorgcpimage';
            orgcpuploadimage = orgcpuploadimage.replace("http", "https");

            var siteurl = '<?php echo Router::url('/', true); ?>';


            if ((siteurl.indexOf('localhost') > -1) || (siteurl.indexOf('staging') > -1)) {
                if (siteurl.indexOf('https') > -1) {
                    siteurl = siteurl.replace("https", "http");
                }
            }else if (siteurl.indexOf('ndorse.net') > -1) {
                if (siteurl.indexOf('https') > -1) {

                } else {
                    siteurl = siteurl.replace("http", "https");
                }
            }


//            else{
//                siteurl = siteurl.replace("https", "http");
//            }

            var imgurl = '<?php echo Router::url('/', true); ?>app/webroot/<?php echo PROFILE_IMAGE_DIR; ?>/';
            imgurl = imgurl.replace("http", "https");

            var orgimgurl = '<?php echo Router::url('/', true); ?>app/webroot/<?php echo ORG_IMAGE_DIR; ?>/';
            orgimgurl = orgimgurl.replace("http", "https");

            var Give_Admin_Control = '<?php echo Configure::read("Give_Admin_Control"); ?>';
            var Revoke_Admin_Control = '<?php echo Configure::read("Revoke_Admin_Control"); ?>';

            var Give_Elite_Control = '<?php echo Configure::read("Give_Elite_Control"); ?>';
            var Revoke_Elite_Control = '<?php echo Configure::read("Revoke_Elite_Control"); ?>';
            
            var Give_DAISY_Control = '<?php echo Configure::read("Give_DAISY_Control"); ?>';
            var Revoke_DAISY_Control = '<?php echo Configure::read("Revoke_DAISY_Control"); ?>';
        </script>
        <?php
        echo $this->Html->css("bootstrap.min");

        echo $this->Html->css("signin");
        echo $this->Html->css("style");
        echo $this->Html->css("jquery-ui");
        echo $this->Html->css("simple-sidebar");

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        echo $this->Html->script('jquery.min');
        echo $this->Html->script('jquery.form');
        echo $this->Html->script('jquery.validate');
        echo $this->Html->script('custom');
        echo $this->Html->script('common');
        echo $this->Html->script('ndorse');
        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('bootstrap');
        echo $this->Html->script('bootbox.min');

        //echo $this->Html->script('jquery-latest');
        echo $this->Html->script('jquery.tablesorter');
        ?>

    </head>
    <body>
        <?php
        echo $this->Element("headerorg");
        ?>
        <?php echo $this->fetch('content'); ?>
    </div>
</div>
</div>
<?php #echo $this->element('sql_dump'); ?>
</body>
</html>

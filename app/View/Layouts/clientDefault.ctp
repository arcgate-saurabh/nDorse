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
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="google-site-verification" content="f0Afx96zB2-kLMvH_8xh84MH7Fr8scbLMt1U2i3BPRc" />
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php //echo $cakeDescription  ?>
            <?php //echo $this->fetch('title'); ?>
            nDorse
        </title>
        <meta name="description" content="nDorse - Web app login page" />
        <script type='text/javascript'>
            var siteurl = '<?php echo Router::url('/', true); ?>';

            //if ((siteurl.indexOf('localhost') > -1) || (siteurl.indexOf('staging') > -1)) {
            if (siteurl.indexOf('localhost') > -1) {
                if (siteurl.indexOf('https') > -1) {
                    siteurl = siteurl.replace("https", "http");
                }

            }else if (siteurl.indexOf('ndorse.net') > -1) {
                if (siteurl.indexOf('https') > -1) {

                } else {
                    //siteurl = siteurl.replace("http", "https");
                }
            }
            
            
//            else {
//                siteurl = siteurl.replace("https", "http");
//            }

            var imgurl = '<?php echo Router::url('/', true); ?>app/webroot/<?php echo PROFILE_IMAGE_DIR; ?>/';
            var orgimgurl = '<?php echo Router::url('/', true); ?>app/webroot/<?php echo ORG_IMAGE_DIR; ?>/';
            var referer = '<?php echo $referer; ?>';
            var endorser = '<?php echo ENDORSER; ?>';
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
        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('bootstrap');
        echo $this->Html->script('bootbox.min');
        if (isset($jsIncludes)) {
            foreach ($jsIncludes as $jsincluded) {
                echo $this->Html->script($jsincluded);
            }
        }
        ?>

    </head>
    <body>
        <input type="hidden" id="refreshed" value="no">
        <?php echo $this->fetch('content'); ?>
    </div>
</div>
</div>
<script>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-79504250-3', 'auto');
    ga('send', 'pageview');

</script>
<?php //echo $this->element('sql_dump');  ?>
</body>
</html>

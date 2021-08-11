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
        <script type='text/javascript'>

            var siteurl = '<?php echo Router::url('/', true, true); ?>';



            if (siteurl.indexOf('localhost') > -1) {
                if (siteurl.indexOf('https') > -1) {
                    //siteurl = siteurl.replace("http", "https");
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
            //commented for image upload issue
            //imgurl = imgurl.replace("http", "https");

            var orguploadimage = '<?php echo Router::url('/', true); ?>setOrgImage';
            if (orguploadimage.indexOf('localhost') > 0) {
                orguploadimage = orguploadimage.replace("http", "https");
            }


            var orgimgurl = '<?php echo Router::url('/', true); ?>app/webroot/<?php echo ORG_IMAGE_DIR; ?>/';
            //commented for image upload issue
            //orgimgurl = orgimgurl.replace("http", "https");

            var referer = '<?php echo $referer; ?>';
        </script>
        <script type="text/javascript">var switchTo5x = true;</script>

        <?php if (!isset($noLeftMenu) || !$noLeftMenu) { ?>
                                    <!--<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script>-->
            <!--<script type="text/javascript">stLight.options({publisher: "3ef8011e-124e-4391-a8a8-7261f7bb142b", doNotHash: false, doNotCopy: false, hashAddressBar: false});</script>-->
            <?php
        }
        echo $this->Html->css("bootstrap.min");
        echo $this->Html->css("signin");
        echo $this->Html->css("style");
        echo $this->Html->css("jquery-ui");
        echo $this->Html->css("simple-sidebar");
        echo $this->Html->css("nano");
        echo $this->Html->css("jquery-confirm.min");
        echo $this->Html->css("bootstrap-datetimepicker"); //added @7-dec-2017 by babulal prasad
        echo $this->Html->css("font-awesome.min"); //added @7-dec-2017 by babulal prasad
        echo $this->Html->css("flaticon"); //added @23-FEB-2021 by babulal prasad

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        
        echo $this->Html->script('jquery.min');
        echo $this->Html->script('jquery.form');
        echo $this->Html->script('jquery.validate');
        echo $this->Html->script('jquery-confirm.min');
        echo $this->Html->script('common');
        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('bootstrap');
        echo $this->Html->script('bootbox.min');
        echo $this->Html->script('clientndorse');
        echo $this->Html->script('nano');
        echo $this->Html->script('exif.js');
//        echo $this->Html->script('js_bootstrap-datetimepicker');
        echo $this->Html->script('moment'); //added @7-dec-2017 by babulal prasad
        echo $this->Html->script('bootstrap-datetimepicker'); //added @7-dec-2017 by babulal prasad
        if (isset($jsIncludes)) {
            foreach ($jsIncludes as $jsincluded) {
                echo $this->Html->script($jsincluded);
            }
        }

        echo $this->fetch('meta');
        echo $this->fetch('css');
        echo $this->fetch('script');
        $loggedinUser = AuthComponent::user();
        if (!empty($loggedinUser) && isset($loggedinUser['current_org'])) {
            $currntOrgArray = json_decode(json_encode($loggedinUser['current_org']), true);
            
            ?>
        <script type="text/javascript">
            //document ready start
            $(document).ready(function () {
                timelyUpdate("<?php echo $portal; ?>");
                //generateApiSessionLog("<?php //echo $portal; ?>");

            });//document ready end
        </script>
        <?php } else { ?>
        <script type="text/javascript">
            //document ready start
            $(document).ready(function () {
                acceptRequestUpdate("<?php echo $portal; ?>");

            });//document ready end
        </script>
        <?php }
        ?>

    </head>
    <?php
    $bodyClass = '';
    $paramsaction = trim($this->params["action"]);
    if ($paramsaction == 'daisy') {
        $bodyClass = 'daisy_portal';
    }
    ?>
    <body class="<?php echo $bodyClass; ?> preview">
        <input type="hidden" id="refreshed" value="no">
        <div class="marg-top" style="margin-top: 30px;"></div>
        <?php echo $this->element('client_header'); ?>
        <?php if (!isset($noLeftMenu) || !$noLeftMenu) { ?>
        <div class="container col-md-10 col-md-offset-2 col-sm-9 col-sm-offset-3">
            <?php } else {  
                
                ?>
            <div class="container">
                <?php } ?>

                <?php echo $this->fetch('content'); ?>
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

                var colorCodeLight = '<?php echo $currntOrgArray['header_footer_color_light'];?>';
                var colorCodeDark = '<?php echo $currntOrgArray['header_footer_color_dark'];?>';
                var backgroundLight = '<?php echo $currntOrgArray['background_color_light'];?>';
                var backgroundDark = '<?php echo $currntOrgArray['background_color_dark'];?>';
                var fontColor = '<?php echo $currntOrgArray['font_color'];?>';
                var buttonColor = '<?php echo $currntOrgArray['button_color'];?>';
                //new color branding code starts here 
                var cardColorLight = '<?php echo $currntOrgArray['card_color_light'];?>';
                var cardColorDark = '<?php echo $currntOrgArray['card_color_dark'];?>';
                //ends here

                $(document).find('.orange-bg').addClass('headerFooterBGLight');
                $('.orange-bg').addClass('headerFooterBGLight');
                $('.client-nav').addClass('previewNav');
                $('.btn-orange, .btn-orange-small').addClass('CustomBtnColor');
                $('.tTip').addClass('headerFooterBG');
                $('.post-thumb h6, .nDorsed-by, .range, .nDorse-Details-msg .mesg').addClass('commonFont');

                //
                //$('.live-feeds').addClass('live-feeds');
                //
                $('.Dear-Details, .live-feeds, .search-icn input').css('border-color', '#' + colorCodeLight);

                $('.headerFooterBG').css('background-image', 'linear-gradient(to bottom, #' + colorCodeLight + ',  #' + colorCodeDark + ')').css('border', 'none');
                $('.headerFooterBGLight').css('background-color', '#' + colorCodeLight);

                $('.preview').css('background', 'radial-gradient(at 65% 50%, #' + backgroundLight + ' , #' + backgroundDark + ' 60%)');
                $('.previewNav').css('background', 'radial-gradient(at 30% 30%,  #' + backgroundDark + ' 30%, #' + backgroundLight + ')');

                //new color branding code for ndorsement starts here 
                $('.new-bg-feeds').css('background', 'radial-gradient(at 50% 50%, #' + cardColorLight + ' , #' + cardColorDark + ' 60%)');
                //$('.Dear-Details').css('background', 'radial-gradient(at 65% 50%, #' + cardColorLight + ' , #' + cardColorDark + ' 60%)');
                //ends here

                $('.bar-mob').css('background', '#' + colorCodeLight);
                $('.commonFont, .commonFont a').css('color', '#' + fontColor + ' !important');



                //$('.CustomBtnColor').css('background', '#' + buttonColor + ' !important');
                //$('.CustomBtnColor').css('linear-gradient(to bottom', '#' + buttonColor + ' !important)');
                $('.CustomBtnColor, body.preview .fileUpload label').css('background', '#' + buttonColor);
                $('body.preview .fileUpload ~ h3').css('color', '#' + buttonColor);

                $('.orgfilterradio').css('border-color', '#' + colorCodeLight).css('background-color', '#' + colorCodeLight);
                $('.orgfilterradio.active').css('border-color', '#' + colorCodeLight).css('background-color', '#' + backgroundLight);

                $('.sidebar-brand,  .sidebar-brand a').css('color', '#' + fontColor + ' !important');

                $('.sidebar-brand,  .sidebar-brand a').on('mouseout', function () {
                    $('.sidebar-brand,  .sidebar-brand a').css('color', '#' + fontColor + ' !important');
                });

                $('.sidebar-brand,  .sidebar-brand a').on('hover', function () {
                    $('.sidebar-brand,  .sidebar-brand a').css('color', '#' + colorCodeLight + ' !important');
                });



            </script>
            <?php //echo $this->element('sql_dump');  ?>
    </body>
</html>

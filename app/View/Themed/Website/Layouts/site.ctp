<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>
            <?php //echo $cakeDescription ?>
            <?php echo $this->fetch('title'); ?>
        </title>
        <?php if (isset($description) && $description != "") { ?>
            <meta name="description" content="<?php echo $description; ?>" />
        <?php } ?>
        <meta name="google-site-verification" content="f0Afx96zB2-kLMvH_8xh84MH7Fr8scbLMt1U2i3BPRc" />
        <script type='text/javascript'>
            var siteurl = '<?php echo Router::url('/', true); ?>';

            if ((siteurl.indexOf('localhost') > -1) || (siteurl.indexOf('staging') > -1)) {
                if (siteurl.indexOf('https') > -1) {
                    siteurl = siteurl.replace("http", "https");
                }
            } else if (siteurl.indexOf('ndorse.net') > -1) {
                if (siteurl.indexOf('https') > -1) {

                } else {
                    siteurl = siteurl.replace("http", "https");
                }
            }
//            else {
//                siteurl = siteurl.replace("https", "http");
//            }
            //siteurl = siteurl.replace("http", "https");
        </script>
        <?php
        echo $this->Html->css("bootstrap.min");
        echo $this->Html->css("style");
        echo $this->Html->script('jquery.min');
        echo $this->Html->script('staticsite');
        echo $this->Html->script('bootstrap');
        echo $this->Html->script('jquery.form');
        echo $this->Html->script('jquery-ui');
        echo $this->Html->script('bootbox.min');
        echo $this->Html->script('jquery.validate');
        ?>
    </head>
    <?php $paramsaction = trim($this->params["action"]); ?>
    <body>
        <div class="container">
            <nav class="main-nav navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
                    </div>
                    <div class="collapse navbar-collapse static-nav" id="myNavbar" style="">
                        <ul class="nav navbar-nav navbar-right">
                            <li><?php echo $this->Html->Link("Home", array("controller" => "site", "action" => "index"), ($paramsaction == "index") ? array("class" => "site-active") : ""); ?></li>
                            <li><?php echo $this->Html->Link("About", array("controller" => "site", "action" => "about"), ($paramsaction == "about") ? array("class" => "site-active") : ""); ?></li>
                            <li><?php echo $this->Html->Link("Our Clients", array("controller" => "site", "action" => "client"), ($paramsaction == "client") ? array("class" => "site-active") : ""); ?></li>
                            <li><?php echo $this->Html->Link("Contact Us", array("controller" => "site", "action" => "contact"), ($paramsaction == "contact") ? array("class" => "site-active") : ""); ?></li>
                            <li><?php echo $this->Html->Link("Admin Portal", array("controller" => "users", "action" => "login"), array("target" => "_blank", "escape" => false)); ?></li>
                            <li><?php echo $this->Html->Link("Web App", array("controller" => "client", "action" => "login"), array("target" => "_blank", "escape" => false)); ?></li>
                            <li><?php echo $this->Html->Link("How To Videos", array("controller" => "site", "action" => "how_to"), ($paramsaction == "how_to") ? array("class" => "site-active") : ""); ?></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <?php echo $this->fetch('content'); ?>
        <?php //echo $this->element('sql_dump'); ?>
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
    </body>
</html>
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
                    siteurl = siteurl.replace("https", "http");
                }
            } else if (siteurl.indexOf('ndorse.net') > -1) {
                if (siteurl.indexOf('https') > -1) {

                } else {
                    siteurl = siteurl.replace("http", "https");
                }
            }

            //siteurl = siteurl.replace("http", "https");
        </script>
        <?php
        echo $this->Html->css("bootstrap.min");
        echo $this->Html->css("style");
        //echo $this->Html->css("nanoscroller");
//        echo $this->Html->script('jquery.min');
//        echo $this->Html->script('jquery-3.3.1.min');
//        echo $this->Html->script('jquery.min.1.10.2');
        echo $this->Html->script('jquery-1.11.0.min');
        //echo $this->Html->script('jquery.nanoscroller');
//            echo $this->Html->script('staticsite');
        echo $this->Html->script('bootstrap');
        echo $this->Html->script('addGuestnDorse');
        echo $this->Html->script('bootbox.min');
//            echo $this->Html->script('jquery.validate'); 
        ?>

    </head>
    <?php $paramsaction = trim($this->params["action"]); ?>
    <body>
        <div class="container">
            <!--            <nav class="main-nav navbar navbar-default">
                            <div class="container-fluid">
                                <div class="navbar-header">
                                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                                        <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> 
                                    </button>
                                </div>
                            </div>
                        </nav>-->
        </div>
        <?php echo $this->fetch('content'); ?>
        <?php //echo $this->element('sql_dump'); ?>
    </body>
</html>
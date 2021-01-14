<!--Fancybox Starts -->
<?php
echo $this->Html->css('/js/fancybox/jquery.fancybox.css');
echo $this->Html->script('fancybox/jquery-1.10.2.min.js');
echo $this->Html->script('fancybox/jquery.fancybox.js');
echo $this->Html->script('fancybox/fancybox/jquery.fancybox.pack.js');
echo $this->Html->script('fancybox/jquery.fancybox-media.js');
?>
<script type="text/javascript">
    $(document).ready(function () {
        /*
         *  Simple image gallery. Uses default settings
         */

        $('.fancybox').fancybox();

        /*
         *  Different effects
         */

        // Change title type, overlay closing speed
        $(".fancybox-effects-a").fancybox({
            helpers: {
                title: {
                    type: 'outside'
                },
                overlay: {
                    speedOut: 0
                }
            }
        });

        // Disable opening and closing animations, change title type
        $(".fancybox-effects-b").fancybox({
            openEffect: 'none',
            closeEffect: 'none',
            helpers: {
                title: {
                    type: 'over'
                }
            }
        });

        // Set custom style, close if clicked, change title type and overlay color
        $(".fancybox-effects-c").fancybox({
            wrapCSS: 'fancybox-custom',
            closeClick: true,
            openEffect: 'none',
            helpers: {
                title: {
                    type: 'inside'
                },
                overlay: {
                    css: {
                        'background': 'rgba(238,238,238,0.85)'
                    }
                }
            }
        });

        // Remove padding, set opening and closing animations, close if clicked and disable overlay
        $(".fancybox-effects-d").fancybox({
            padding: 0,
            openEffect: 'elastic',
            openSpeed: 150,
            closeEffect: 'elastic',
            closeSpeed: 150,
            closeClick: true,
            helpers: {
                overlay: null
            }
        });

        /*
         *  Button helper. Disable animations, hide close button, change title type and content
         */

        $('.fancybox-buttons').fancybox({
            openEffect: 'none',
            closeEffect: 'none',
            prevEffect: 'none',
            nextEffect: 'none',
            closeBtn: false,
            helpers: {
                title: {
                    type: 'inside'
                },
                buttons: {}
            },
            afterLoad: function () {
                this.title = 'Image ' + (this.index + 1) + ' of ' + this.group.length + (this.title ? ' - ' + this.title : '');
            }
        });


        /*
         *  Thumbnail helper. Disable animations, hide close button, arrows and slide to next gallery item if clicked
         */

        $('.fancybox-thumbs').fancybox({
            prevEffect: 'none',
            nextEffect: 'none',
            closeBtn: false,
            arrows: false,
            nextClick: true,
            helpers: {
                thumbs: {
                    width: 50,
                    height: 50
                }
            }
        });

        /*
         *  Media helper. Group items, disable animations, hide arrows, enable media and button helpers.
         */
        $('.fancybox-media')
                .attr('rel', 'media-gallery')
                .fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                    prevEffect: 'none',
                    nextEffect: 'none',
                    //arrows : false,
                    helpers: {
                        media: {},
                        buttons: {}
                    }
                });

        /*
         *  Open manually
         */

        $("#fancybox-manual-a").click(function () {
            $.fancybox.open('1_b.jpg');
        });

        $("#fancybox-manual-b").click(function () {
            $.fancybox.open({
                href: 'iframe.html',
                type: 'iframe',
                padding: 5
            });
        });

        $("#fancybox-manual-c").click(function () {
            $.fancybox.open([
                {
                    href: '1_b.jpg',
                    title: 'My title'
                }, {
                    href: '2_b.jpg',
                    title: '2nd title'
                }, {
                    href: '3_b.jpg'
                }
            ], {
                helpers: {
                    thumbs: {
                        width: 75,
                        height: 50
                    }
                }
            });
        });


    });
</script>
<!--Fancybox Ends -->

<div class="container">
  <div class="clients">
    <div class="pull-left"> <?php echo $this->Html->Image("/images/logo.png", array("alt" => "", "align" => "left", "width" => "100")); ?> </div>
    <div class="pull-left">
      <h2>HOW TO VIDEOS</h2>
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="our-clients">
    <div class="vid-gallery" >
      <div class="thumb"> <a class="fancybox-media" href="https://www.youtube.com/embed/JIHwpj8K-0c" data-fancybox-group="gallery" title="How to create an account"> <?php echo $this->Html->Image('/images/thumb01.jpg', array("alt" => "", "align" => "left")); ?> <br />
        <h5>How to Create an Account</h5>
        </a></div>
      <div class="thumb"> <a class="fancybox-media" href="https://www.youtube.com/watch?v=xpbfqmsLiO8&feature=youtu.be" data-fancybox-group="gallery" title="How to nDorse a Team Member"><?php echo $this->Html->Image('/images/thumb02.jpg', array("alt" => "", "align" => "left")); ?>
        <h5>How to nDorse a Team Member</h5>
        </a> </div>
      <div class="thumb"> <a class="fancybox-media" href="https://youtu.be/eIgC6RUCOhw" data-fancybox-group="gallery" title="How to nDorse a Team Member"><?php echo $this->Html->Image('/images/thumb03.jpg', array("alt" => "", "align" => "left")); ?>
        <h5>Types of nDorsements</h5>
        </a> </div>
        <div class="thumb"> <a class="fancybox-media" href="https://youtu.be/oXeg7fNaNPw" data-fancybox-group="gallery" title="How to nDorse a Team Member"><?php echo $this->Html->Image('/images/thumb04.jpg', array("alt" => "", "align" => "left")); ?>
        <h5>Introduction to nDorse App and Admin Portal</h5>
        </a> </div>
      <div class="clearfix"></div>
      
      <div class="thumb" > <a class="fancybox-media" href="https://youtu.be/1yMcqowM_Ns" data-fancybox-group="gallery" title="How to reset or change passwords"><?php echo $this->Html->Image('/images/thumb05.jpg', array("alt" => "", "align" => "left")); ?>
        <h5>How to reset or change passwords</h5>
        </a> </div>
        
        <div class="thumb" > <a class="fancybox-media" href="https://youtu.be/GahXq1QxbDw" data-fancybox-group="gallery" title="How can Admin change user password?"><?php echo $this->Html->Image('/images/thumb06.jpg', array("alt" => "", "align" => "left")); ?>
        <h5>How can Admin change user password?</h5>
        </a> </div>

        <div class="thumb" > <a class="fancybox-media" href="https://youtu.be/XOxk7tHzOqs" data-fancybox-group="gallery" title="Signing up for nDorse - Email Invitation to Live Feed"><?php echo $this->Html->Image('/images/thumb07.jpg', array("alt" => "", "align" => "left")); ?>
        <h5>Signing up for nDorse - Email Invitation to Live Feed</h5>
        </a> </div>
        
        
        
    </div>
    <div class="clearfix"></div>
  </div>
</div>
<?php echo $this->Element("footersite"); ?> 
<?php
$data = array(
    "textcenter" => "Featured Video Settings",
    "righttabs" => "3",
    "orgid" => $orgDetail['Organization']['id'],
    "video_feature" => $orgDetail['Organization']['featured_video_enabled'],
    "customer_portal" => $orgDetail['Organization']['allow_customer_portal'],
    "daisy_portal" => $orgDetail['Organization']['enable_daisy_portal']
);
$headerpage = ($authUser["role"] == 1) ? 'header' : 'headerorg';
echo $this->Element($headerpage, array('data' => $data));
//pr($orgDetail['Organization']['image']); exit;
?>

<!--Fancybox Starts -->
<?php
echo $this->Html->css('/js/fancybox/jquery.fancybox.css');
echo $this->Html->script('fancybox/jquery-1.10.2.min.js');
echo $this->Html->script('fancybox/jquery.fancybox.js');
echo $this->Html->script('fancybox/jquery.fancybox.pack.js');
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

<p><?php echo $this->Session->Flash(); ?></p>

<section>


<!--<section>-->
    <div class="customerPortal" id="fnamelname">
        <div class="">
            <div class="createEditOrg">
                <div class="stats">
                    <h2><?php echo $orgDetail['Organization']['name']; ?></h2>
                </div>
                <div class="col-lg-12 row">
                    <?php echo $this->Form->create('Orgphoto', array('url' => array('controller' => 'users', 'action' => 'setorgcpImage'))); ?>
                    <div class="">
                        <?php
                        //$orgDetail
                        //                            pr($orgDetail); exit;
                        if (isset($orgDetail)) {
                            if (isset($orgDetail['Organization']['cp_logo']) && $orgDetail['Organization']['cp_logo'] != '') {
                                $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR . $orgDetail['Organization']['cp_logo'];
                                echo $this->Html->image($org_imagenew, array('width' => '175', 'id' => 'org_image'));
                            } else if (isset($orgDetail['Organization']['image']) && $orgDetail['Organization']['image'] != '') {
                                $org_imagenew = Router::url('/', true) . "app/webroot/" . ORG_IMAGE_DIR . $orgDetail['Organization']['image'];
                                echo $this->Html->image($org_imagenew, array('width' => '175', 'id' => 'org_image'));
                            } else {
                                echo $this->Html->image('comp_pic.png', array('width' => '214', 'id' => 'org_image'));
                            }
                        }
                        ?>
                        <?php
                        echo $this->Form->input('cp_logo', array(
                            'type' => 'file',
                            'id' => 'photo',
                            'label' => false,
                            'class' => 'btn_uplaod_file hidden'
                        ));
                        ?>
                    </div>
                    <?php echo $this->Form->end(); ?>
                </div>
            </div>
        </div>
        <?php
        echo $this->Form->input('id', array('type' => 'hidden', 'value' => $orgDetail['Organization']['id']));
        echo $this->Form->input('image', array('type' => 'hidden', 'id' => 'org_image_name', 'value' => $orgDetail['Organization']['image']));
        ?>
    </div>
    <div class="clearfix"></div>
</section>
<section>
    <div class="stats">
        <div class="active-videos">
            <h3><span class="heading_status_type">Active Videos</span></h3>
            <button class="btn statusvideobttn btn-success btn-sm ml15 Showing active" data-name="Active Videos" disabled="disabled" data-value="1">Showing</button>
            <button class="btn statusvideobttn btn-danger btn-sm ml15 Rejected" data-name="Rejected Videos" data-value="3">In-active</button>
        </div>
    </div>
</section>
<!--<input type="hidden" id="totalrecords" value="<?php echo $totalrecords; ?>">-->
<input type="hidden" id="org_id" value="<?php echo $orgDetail['Organization']['id']; ?>">
<input id="pagename" value="guestendorsements" type="hidden">
<div style="text-align: center"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>

<div id="searchendorsement">
    <div class="row col-md-12 ">
    </div>
    <?php
    //==============binding element to show data
    //pr($orgDetail);

    echo $this->Element("livesearchdatavideo", array("orgVideoListArray" => $orgVideoListArray));
    ?>


</div>
<script>
    
    
    //clicking outside will close the arrow box
    $(document).mouseup(function (e) {
        var container = $(".arrow_box");
        var clnew = $(e.target).attr('src');
        if (!clnew && !container.is(e.target) && container.has(e.target).length === 0) {
            container.hide();
        }
    });
    
    $(document).on("click", ".dots", function (e) {
        var rel = $(this).attr("rel");
        $("." + rel).toggle();
        $('.dots').not(this).each(function () {
            $(this).siblings(".arrow_box").hide();
        });
    });
    
    
    
    //Added By Babulal Prasad @29-may-2018 //To filter guest nDorsement result
    $(".statusvideobttn").click(function () {

        var listStatus = $(this).attr("data-value");
        var statusClass = $(this).attr("data-name");
        //var totalrecords = $("#searchendorsement section").length;
        var totalrecords = 0;
        var orgid = $("#org_id").val();
//        console.log("total records :" + $("#totalrecords").val() + "<=" + totalrecords);
        console.log("orgid :" + orgid);
        console.log("listStatus :" + listStatus);
//        return false;
        $(".statusvideobttn").attr("disabled", false);
        $("." + statusClass).attr("disabled", true);
        $(".heading_status_type").html(statusClass);
        $(".hiddenloader").removeClass("hidden");

        if (siteurl.indexOf('localhost') > -1) {
            if (siteurl.indexOf('https') > -1) {
                siteurl = siteurl.replace("http", "https");
            }
        }

        setTimeout(function () {
            $.ajax({
                type: "POST",
                url: siteurl + 'ajax/loadmorevideos',
                data: {status: listStatus, orgId: orgid},
                success: function (data, xhr) {
                    req_sent = false;
                    if (data == "") {
                        $(".hiddenloader").remove();
                        return false;
                    }
                    $("#searchendorsement").html(data);
                    $(".hiddenloader").addClass("hidden");
                },
                fail: function (data, xhr) {
                    $("." + statusClass).attr("disabled", false);
                    $(".hiddenloader").addClass("hidden");
                }
            });
        }, 1000)
    });


    $("#CustomerportalsettingFormSubmit").click(function () {
        $("#OrganizationCustomerportalsettingForm").submit();
    });

    function myFunction() {
        var $temp = $("<input>");
        $("body").append($temp);
        var myCode = $('#portal_link').html();
        $temp.val(myCode).select();
        document.execCommand("copy");
        $temp.remove();

        var tooltip = document.getElementById("myTooltip");
        //tooltip.innerHTML = "Link Copied: " + myCode;
        tooltip.innerHTML = "Link Copied";
    }

    function outFunc() {
        var tooltip = document.getElementById("myTooltip");
        tooltip.innerHTML = "Copy to clipboard";
    }

</script>
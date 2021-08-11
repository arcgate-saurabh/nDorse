<?php

echo $this->Html->script('jquery.colorpicker');
echo $this->Html->css("jquery.colorpicker");
echo $this->Html->script('jscolor');
?>

<?php
//pr($org_data);
$data = array(
    "textcenter" => "Organization Theme Settings",
    "righttabs" => "3",
    "orgid" => $org_data['Organization']['id'],
    "video_feature" => $org_data['Organization']['featured_video_enabled'],
    "customer_portal" => $org_data['Organization']['allow_customer_portal'],
    "daisy_portal" => $org_data['Organization']['enable_daisy_portal']
);
$headerpage = ($authUser["role"] == 1) ? 'header' : 'headerorg';
if ($authUser["role"] == 2 || $authUser["role"] == 6) {
    $data['auth_users'] = $authUser;
}
echo $this->Element($headerpage, array('data' => $data));
$orgdetails = array(
    "id" => $org_data['Organization']['id'],
    "image" => $org_data['Organization']['image'],
    "name" => $org_data['Organization']['name'],
    "sname" => $org_data['Organization']['short_name'],
    "secret_code" => $org_data['Organization']['secret_code'],
    "street" => $org_data['Organization']['street'],
    "city" => $org_data['Organization']['city'],
    "state" => $org_data['Organization']['state'],
    "zip" => $org_data['Organization']['zip'],
    "country" => $org_data['Organization']['country'],
    "status" => $org_data['Organization']['status'],
);
$org_image = $org_data['Organization']['image'];
$orgname = $org_data['Organization']['name'];
$orgid = $org_data['Organization']['id'];

 echo $this->Form->create("Organization", array("enctype" => "multipart/form-data"));
?>
<div id="themesEditor">
    <p><?php echo $this->Session->Flash(); ?></p>
    <div class="head">
        <h2><?php echo $this->Html->link($org_data['Organization']['name'],array('controller'=>'organizations','action'=>'info',$org_data['Organization']['id'])); ?>
        </h2> <h4>Choose Theme Colors</h4></div>


    <div class="themeChange">
        <div class="themeBox">
            <div class="themeSet">
                <div class="labelCus">Header & Footer Color:</div>
                <div>
                    <input class="jscolor" name="header_footer_color_light" value="<?php echo $org_data['Organization']['header_footer_color_light'];?>" id="headerFooterLight" />
                    <input class="jscolor" name ="header_footer_color_dark" value="<?php echo $org_data['Organization']['header_footer_color_dark'];?>" id="headerFooterDark" />
                </div>
            </div>
        </div>
        <div class="themeBox">
            <div class="themeSet">
                <div class="labelCus">Background Color:</div>
                <div>
                    <input class="jscolor" name ="background_color_light" value="<?php echo $org_data['Organization']['background_color_light'];?>" id="backgroundLight" />
                    <input class="jscolor" name ="background_color_dark" value="<?php echo $org_data['Organization']['background_color_dark'];?>" id="backgroundDark" />
                </div>
            </div>
        </div>
        <div class="themeBox">
            <div class="themeSet">
                <div class="labelCus">Font Color:</div>
                <input class="jscolor" name ="font_color" value="<?php echo $org_data['Organization']['font_color'];?>" id="fontColor" />
            </div>
        </div>
        <div class="themeBox">
            <div class="themeSet">
                <div class="labelCus">Button Color:</div>
                <input class="jscolor" name ="button_color" value="<?php echo $org_data['Organization']['button_color'];?>" id="buttonColor" />

            </div>
        </div>

        <div class="themeBox">
            <div class="themeSet">
                <div class="labelCus">Card Color:</div>
                <div>
                    <input class="jscolor" name ="card_color_light" value="<?php echo $org_data['Organization']['card_color_light'];?>" id="cardColorLight" />
                    <input class="jscolor" name ="card_color_dark" value="<?php echo $org_data['Organization']['card_color_dark'];?>" id="cardColorDark" />
                </div>
            </div>
        </div>

        <div class="themeBox">
            <a href="javascript:void(0);" class="resetThemeToDefault resetLink">Set Default</a>
        </div>
    </div>

</div>

<div id="themePreview">
    <div class="row">
        <div class="col-sm-7" style="margin-left: 50px;">
            <div class="deskPreview">
                <h2>Web Preview</h2>
                <div class="preview" style="">
                    <div class="container-fluid header-bg-nav headerFooterBG">
                        <div class=" headerinfo">
                            <div class="pull-left menu">
                                <div class="title-org">
                                    <span style="margin-top:20px; font-size:16px;" class="commonFont">Organizations</span>
                                </div>
                            </div>
                            <div class="RightTabs">
                                <span id="refresh">
                                    <a href="javascript:void(0)">
                                        <?php echo $this->Html->image('refresh.png', array('width' => 36, 'alt' => "refresh")); ?>
                                    </a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mainView">
                        <div class="col-sm-4 col-md-3 client-nav sidebar">
                            <ul class="sidebar-nav previewNav">
                                <li class="sidebar-brand commonFont">
                                    <a href="javascript:void(0);">Organizations</a>   
                                </li>
                                <li class="sidebar-brand commonFont">
                                    <a href="javascript:void(0);">Set Up New Organization</a>   
                                </li>
                                <li class="sidebar-brand commonFont">
                                    <a href="javascript:void(0);">Global Settings</a></li>
                                <li class="sidebar-brand commonFont"><a href="javascript:void(0);">Add Super Admin</a></li>
                                <li class="sidebar-brand commonFont"><a href="javascript:void(0);">Reset Password</a></li>
                                <li class="sidebar-brand commonFont"><a href="javascript:void(0);">Stats</a></li>
                                <li class="sidebar-brand commonFont"><a href="javascript:void(0);">Reports</a></li>
                                <li class="sidebar-brand commonFont"><a href="javascript:void(0);">Pending Announcements</a></li>
                                <li class="sidebar-brand commonFont"><a href="javascript:void(0);">Search Users</a></li>                                    
                            </ul>
                            <div class="logout headerFooterBG"> 
                                <div class="clearfix"></div>
                                <div class="poweredBy">Powered By <?php echo $this->Html->image('logo-excel.png', array('width' => '35', 'alt' => 'img', 'class' => 'like-img like-img-post')); ?></div>
                  
                                    </div>
                        </div>
                        <!--#Right Side-->
                        <div class="container col-md-9 col-md-offset-3 col-sm-8 col-sm-offset-4">
                            <div class="Dear-Details new-bg-feeds" id="feed_364" post_id="364">
                                <div class="Name-Post "> 
                                    <div class="namenimg">
                                        <!--<img alt="" class="img-circle hand show-user-profile" src="" width="50px" height="50px" align="left" title="Babulal Prasad" data-user-id="2926" data-logged-id="13766">-->
                                        <?php echo $this->Html->image('user.png', array('class' => 'img-circle', 'width' => '50px', 'align' => 'left', 'title' => 'Babulal Prasad')); ?>
                                        <h4 class="range">Babulal Prasad</h4>
                                        <h5 class="commonFont">Er.</h5>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="data-url hand live-feeds-post" id="feed_364" post_id="364">
                                        <h3 class="commonFont">Post Test</h3>
                                        <p class="commonFont">This is post testing.</p>
                                        <div class="clearfix"></div>
                                        <div class="detail-img">
                                            <div class="img-cont">

                                            </div>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="orange-bg no-hand headerFooterBGLight">
                                    <div class="col-md-4 text-left"> 
                                        <a class="show-me-popup" href="javascript:void(0);"> 
                                            <?php echo $this->Html->image('like.png', array('width' => '20', 'alt' => 'img', 'class' => 'like-img like-img-post')); ?>
                                        </a> 
                                        <span class="show-me-popup likes postlikeslist hand" post="364" like="0" id="likes_364">1 Like</span> 
                                    </div>
                                    <span class="show-popup-flag show-me-popup-new_364" data-toggle="modal" data-target="#one" post="364"></span> 
                                    <div class="col-md-4 text-center"> <span>
                                            Mar 27                                    </span> </div>
                                    <div class="col-md-4 text-right hand" id="feed_364" post_id="364"> 
                                        <?php echo $this->Html->image('post-comnt.png', array('class' => 'marg-right hand', 'width' => '20')); ?>
                                        <span class="comnt-count">0</span>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <!--#-->
                            <div class="live-feeds new-bg-feeds">                      
                                <div class="row hand">
                                    <div class="live-feeds-ndorse" id="feed_48596" endorse_id="48596">


                                        <div class= col-xs-3 text-center">

                                             <img width="64px" height="64px" alt="64x64" class="img-circle endorse-user " user_id="1364" endorse_type="user" src="http://api.ndorse.net/app/webroot/uploads/profile/small/1364_1548768517.jpeg">
                                            <h5>Dilbag Singh Rajput </h5>
                                        </div>
                                        <div class="col-xs-6 text-center">
                                            <div class="feed-vertical"> 
                                                <span style="color:#FFFFFF;">Feedback</span>
                                                <br> <span style="/*! border: 1px solid #F47521; */font-size: 18px;font-weight: bold; white-space:normal" class="btn mt10 mb10  ">"ry / Send a Message (Max. 3000 Characters):
                                                    "</span>                                        </div>
                                        </div>

                                        <div class="col-xs-3 text-center">
                                            <!-- <div class="GuestTag">Guest nDorsment</div> -->
                                            <div class="clearfix"></div>
                                            <img width="64px" height="64px" alt="64x64" class="img-circle endorse-user " user_id="2926" endorse_type="user" src="http://api.ndorse.net/app/webroot/uploads/profile/small/2926_1517637053.jpeg">
                                            <h5>nDorsed by<br>
                                                <span class="nDorsed-by">Babulal R Prasad</span> </h5>

                                        </div>

                                        <div class="clearfix"></div>

                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="orange-bg no-hand headerFooterBGLight">
                                        <div class="col-xs-4"> <a href="javascript:void(0)"> 
                                                <img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/like.png" endorse="48596" like="0" id="likes_endorse_48596" class="like-img like-img-endorse"></a>
                                            <span class="likes hand endorselikeslist" endorse="48596" like="0" id="likes_48596">0 Like </span> </div>
                                        <div class="col-xs-4 text-center"> <span>
                                                Aug 07                                                                                    </span> </div>
                                        <div class="col-xs-4 text-right">
                                            <a href="javascript:void(0)"><img width="20" alt="img" src="<?php echo Router::url('/', true); ?>img/email.png" class="marg-right no-hand"></a>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                </div>
                            </div>
                            <!--#-->
                        </div>
                        <!--#Right Side-->
                    </div>

                    <!--#HTML-->

                    <!--#HTML-->
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="mobPreview">
                <h2>Mobile Preview</h2>
                <div class="preview">
                    <div class="header-bg-nav headerFooterBG" style="border: none;">
                        <?php echo $this->Html->image('theme-img/theme-header.png', array('width' => '100%', 'alt' => 'img')); ?>
                    </div>
                    <!--  <div class="nav-mob">
                         Menu
                     </div> -->
                    <div class="search-mob">
                        <?php echo $this->Html->image('theme-img/theme-search.png', array('width' => '100%', 'alt' => 'img')); ?>
                    </div>
                    <div class="detail-mob new-bg-feeds"  style="border:1px solid rgba(255,255,255,0.2);">
                        <div class="desc-mob">
                            <?php echo $this->Html->image('theme-img/theme-detail.png', array('width' => '100%', 'alt' => 'img')); ?>
                        </div>
                        <div class="bar-mob" style="background: #f47521;">
                            <?php echo $this->Html->image('theme-img/theme-bar.png', array('width' => '100%', 'alt' => 'img')); ?>
                        </div>
                    </div>
                    
                    <div class="detail-mob new-bg-feeds"  style="border:1px solid rgba(255,255,255,0.2);">
                        <div class="desc-mob">
                            <?php echo $this->Html->image('theme-img/theme-detail2.png', array('width' => '100%', 'alt' => 'img')); ?>
                        </div>
                        <div class="bar-mob" style="background: #f47521;">
                            <?php echo $this->Html->image('theme-img/theme-bar.png', array('width' => '100%', 'alt' => 'img')); ?>
                        </div>
                    </div>
                    <div class="poweredBy">Powered By 
                        <?php echo $this->Html->image('logo-excel.png',array("width"=>"20", "alt"=>"img", "class"=>"like-img like-img-post")); ?>
                    </div>
                    <div class="footer-mob headerFooterBG header-bg-nav" style="border: none;">
                        <?php echo $this->Html->image('theme-img/theme-footer.png', array('width' => '100%', 'alt' => 'img')); ?>
                        <ul>
                            <li class="active" style="background:rgba(0,0,0,0.5);"></li>
                            <li></li>
                            <li></li>
                            <li></li>
                            <li></li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<section class="container-fluid footer-bg">
    <div class="container">
        <div class="row">
            <div class="pull-right">
                <button type="button" class="btn btn-default" id="orgformcancel">Cancel</button>
                <button type="button" class="btn btn-default" id="editorgcolorsubmit">Save</button>
            </div>
        </div>
    </div>
</section>
<?php echo $this->Form->end(); ?> 
<script>

    $(document).ready(function () {

        var colorCodeLight = '<?php echo $org_data['Organization']['header_footer_color_light'];?>';
        var colorCodeDark = '<?php echo $org_data['Organization']['header_footer_color_dark'];?>';
        var backgroundLight = '<?php echo $org_data['Organization']['background_color_light'];?>';
        var backgroundDark = '<?php echo $org_data['Organization']['background_color_dark'];?>';
        var fontColor = '<?php echo $org_data['Organization']['font_color'];?>';
        var buttonColor = '<?php echo $org_data['Organization']['button_color'];?>';
        
        //starts here
        var cardColorLight = '<?php echo $org_data['Organization']['card_color_light'];?>';
        var cardColorDark = '<?php echo $org_data['Organization']['card_color_dark'];?>';
        //ends here

        $('.headerFooterBG').css('background-image', 'linear-gradient(to bottom, #' + colorCodeLight + ',  #' + colorCodeDark + ')').css('border', 'none');
        $('.headerFooterBGLight').css('background-color', '#' + colorCodeLight);
        $('.preview').css('background', 'radial-gradient(at 65% 50%, #' + backgroundLight + ' , #' + backgroundDark + ' 60%)');
        $('.previewNav').css('background', 'radial-gradient(at 30% 30%,  #' + backgroundDark + ' 30%, #' + backgroundLight + ')');

        //new color branding code for ndorsement starts here 
        $('.new-bg-feeds').css('background', 'radial-gradient(at 50% 50%, #' + cardColorLight + ' , #' + cardColorDark + ' 60%)');
        //
        $('.bar-mob').css('background', '#' + colorCodeLight);
        $('.commonFont, .commonFont a').css('color', '#' + fontColor + ' !important');

        $('#headerFooterLight').val(colorCodeLight).change();
        $('#headerFooterDark').val(colorCodeDark).mouseleave();
        $('#backgroundLight').val(backgroundLight).trigger('mouseout');
        $('#backgroundDark').val(backgroundDark).mouseout();

        //new color branding code for ndorsement starts here 
        $('#cardColorLight').val(cardColorLight).trigger('mouseout');
        $('#cardColorDark').val(cardColorDark).mouseout();
        //ends here
        $('#fontColor').val(fontColor);
        $('#buttonColor').val(buttonColor);
    });

    $('.jscolor').on('change', function () {
        var colorCode = $(this).val();
        var divSectionId = $(this).attr('id');

        if (divSectionId == 'headerFooterDark' || divSectionId == 'headerFooterLight') {
            var colorCodeDark = $('#headerFooterDark').val();
            var colorCodeLight = $('#headerFooterLight').val();

            $('.headerFooterBG').css('background-image', 'linear-gradient(to bottom, #' + colorCodeLight + ',  #' + colorCodeDark + ')').css('border', 'none');
            $('.headerFooterBGLight').css('background-color', '#' + colorCodeLight);

        }

        var backgroundLight = $('#backgroundLight').val();
        var backgroundDark = $('#backgroundDark').val();

        //color new
        var cardColorLight = $('#cardColorLight').val();
        var cardColorDark = $('#cardColorDark').val();
        //ends here
//        console.log(backgroundLight + " /  " + backgroundDark);
        $('.preview').css('background', 'radial-gradient(at 65% 50%, #' + backgroundLight + ' , #' + backgroundDark + ' 60%)');
        //$('.preview').css('background-image', 'rediial-gradient(90deg, #' + backgroundLight + ' ,  #' + backgroundDark + ' 75%)');
        $('.previewNav').css('background', 'radial-gradient(at 30% 30%,  #' + backgroundDark + ' 30%, #' + backgroundLight + ')');
        //$('.previewNav').css('background-image', 'linear-gradient(90deg, #' + backgroundDark +'  50%,  #' + backgroundLight + ')');
        //$('.preview').css('background-color', 'linear-gradient(to bottom, #' + backgroundLight + ' 50%, #' + backgroundDark + ' 50%);');

        //color branding code for ndorsement starts here
        $('.new-bg-feeds').css('background', 'radial-gradient(at 65% 50%, #' + cardColorLight + ' , #' + cardColorDark + ' 60%)');
        //$('.previewNav').css('background', 'radial-gradient(at 30% 30%,  #' + cardColorDark + ' 30%, #' + cardColorLight + ')');
        //ends here
        $('.bar-mob').css('background', '#' + colorCodeLight);




        var fontColor = $('#fontColor').val();
        $('.commonFont, .commonFont a').css('color', '#' + fontColor + ' !important');

    });

    $('.sidebar-brand').on('mouseover', 'a', function () {
        var colorCodeLight = $('#headerFooterLight').val();
        this.style.color = '#' + colorCodeLight;
    }).on('mouseout', 'a', function () {
        //chain to avoid second selector call
        var fontColor = $('#fontColor').val();
        this.style.color = '#' + fontColor;     //native JS application
    });


    $(".resetThemeToDefault").on('click', function () {
        var colorCodeLight = 'F47521';
        var colorCodeDark = 'ED5B13';
        var backgroundLight = '1C2255';
        var backgroundDark = '0C102F';
        var fontColor = 'FFFFFF';
        var buttonColor = 'ED5B13';
        
        var cardColorLight = '551C22';
        var cardColorDark = '2F0C10';

        $('#headerFooterLight').val(colorCodeLight).change();
        $('#headerFooterDark').val(colorCodeDark).mouseleave();
        $('#backgroundLight').val(backgroundLight).trigger('mouseout');
        $('#backgroundDark').val(backgroundDark).mouseout();
        $('#fontColor').val(fontColor);
        $('#buttonColor').val(buttonColor);

        
        //
        $('#cardColorLight').val(cardColorLight).trigger('mouseout');
        $('#cardColorDark').val(cardColorDark).mouseout();
        //
        $('.jscolor').each(function () {
            $(this).focus();
        });

        $('.headerFooterBG').css('background-image', 'linear-gradient(to bottom, #' + colorCodeLight + ',  #' + colorCodeDark + ')').css('border', 'none');
        $('.headerFooterBGLight').css('background-color', '#' + colorCodeLight);
        
        $('.preview').css('background', 'radial-gradient(at 65% 50%, #' + backgroundLight + ' , #' + backgroundDark + ' 60%)');
        $('.previewNav').css('background', 'radial-gradient(at 30% 30%,  #' + backgroundDark + ' 30%, #' + backgroundLight + ')');
        
        //color branding code for ndorsement starts here
        $('.new-bg-feeds').css('background', 'radial-gradient(at 65% 50%, #' + cardColorLight + ' , #' + cardColorDark + ' 60%)');
        //$('.previewNav').css('background', 'radial-gradient(at 30% 30%,  #' + cardColorDark + ' 30%, #' + cardColorLight + ')');
        //ends here
        $('.bar-mob').css('background', '#' + colorCodeLight);
        $('.commonFont, .commonFont a').css('color', '#' + fontColor + ' !important');
    });

</script>

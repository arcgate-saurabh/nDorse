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
<script>
    $(document).ready(function () {
        $(document).find('.orange-bg').addClass('headerFooterBGLight');
        $('.orange-bg').addClass('headerFooterBGLight');
        $('.client-nav').addClass('previewNav');
        $('.btn-orange, .btn-orange-small').addClass('CustomBtnColor');
        $('.tTip').addClass('headerFooterBG');
        $('.post-thumb h6, .nDorsed-by, .range, .nDorse-Details-msg .mesg').addClass('commonFont');

        $('.Dear-Details, .live-feeds, .search-icn input').css('border-color', '#' + colorCodeLight);

        $('.headerFooterBG').css('background-image', 'linear-gradient(to bottom, #' + colorCodeLight + ',  #' + colorCodeDark + ')').css('border', 'none');
        $('.headerFooterBGLight').css('background-color', '#' + colorCodeLight);

        $('.preview').css('background', 'radial-gradient(at 65% 50%, #' + backgroundLight + ' , #' + backgroundDark + ' 60%)');
        $('.previewNav').css('background', 'radial-gradient(at 30% 30%,  #' + backgroundDark + ' 30%, #' + backgroundLight + ')');
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
        
    });
</script>
<?php echo $this->fetch('content'); ?>


<!-- <img src="<?php //echo Router::url('/', true); ?>img/sidebar_bg.png" class="thumb img1" />-->

<?php //pr($loggedinUser); ?>

<div style="position: absolute; overflow: hidden; width: 100%; height: 100%; z-index: 1">
    <div id="imageGallery" style="height: 100vh">
        <img id="image" src="<?php echo Router::url('/', true); ?>img/login_walkthrough_step_1.jpg" width="100%" />
    </div>


    <div id="gain" style="position: absolute; bottom: 50px; width: 100%; display: flex; justify-content: center; align-items:  center; z-index: 10;">
        <button id="skipped_login_walkthrough" class="btn btn-primary js_skipped_login_walkthrough" data-user-id = "<?php echo $loggedinUser['id']; ?>">Skip</button>
        <!-- <button id="previous" type="button">Previous</button> -->

        <button id="next" type="button" class="btn btn-primary">Next</button>

        <button id="finish" type="button" class=" btn btn-primary js_skipped_login_walkthrough" data-user-id = "<?php echo $loggedinUser['id']; ?>">Finish</button>    
    </div>

</div>

<style>
    .btn {
    display: inline-block;
    padding: 8px 25px;
    margin-bottom: 0;
    font-size: 18px;
    font-weight: 700;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: 4px;
    margin:0 50px;
    font-family: 'Avenir-Book', Arial, Helvetica, sans-serif;
}

@media all and (min-width: 768px) and (max-width: 1100px){
    #imageGallery img {width: 100%; height: 100%;}
}
</style>

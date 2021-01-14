<?php ?>
<!-- Whats New -->
<div class="whats-new">
    <div class="select-date">
        <div class="col-md-3 form-group">
            <label>Select Month and Year Range</label>
            <input type="text" placeholder="Month and Year" id="datepicker_topendorsedate" class="form-control date" readonly="readonly">
        </div>

        <div class="col-md-6 ">
            <button id="topendorsedate" class="btn btn-default">APPLY</button>
            <button id="resettopendorsedate" class="btn btn-default">RESET</button>
        </div>
    </div>
    <section style="">
        <div class="clearfix"></div>
    </section>
</div>
<?php //pr($alldata);?>
<div id="topendorsedata">
    <?php echo $this->Element("whatsnewdata");?>
</div>
<!-- Whats New -->
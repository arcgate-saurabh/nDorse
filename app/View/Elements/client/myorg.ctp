<div class="myorg" id ="orglisting">
        <?php 
        if(isset($orgdata["organization"]) && !empty($orgdata["organization"])){
            echo $this->Element("corganizationslisting");
        }else{?>
            <div class="congratulation">

                <div class=" col-md-12 text-center">

                    <h2>
                        Currently, you have not been assigned an Organization. Please create, join or switch to an Organization. Go to MENU to "Create and/or Join An Org" and then switch to an Organization to set your default Organization.
                    </h2>
                </div>
            </div>
        <?php //echo "<div class = 'nodataavailable'>No Data Available</div>";
        }?>
</div>
<input type="hidden" id="pagename" value="myorg">
<div class="col-md-offset-2 text-center col-md-10"> <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden")); ?> </div>
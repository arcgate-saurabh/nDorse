    <?php
        $data = array(
            "textcenter" => "Organization Owners",
            "righttabs" => "1"
        );
        echo $this->Element('header', array('data' => $data));
    ?>



   

<div class="search-icn"><input type="text" class="form-control" id="searchorgowners" onkeyup="searchorgowners(this.value)" placeholder="SEARCH USERS..."></div>
    <section>
        <div class="row ClientList">
            <table class="table tableusersindex table-condensed table-hover">
                <input type="hidden" id="totaluserrecords" value="<?php echo $totaluserrecords;?>">
                <tr>
                    <th></th>
                    <th></th>
                    <th>Type</th>
                    <th>Payment Method</th>
                    <th>Status</th>
                    <th>Number Of Orgs</th>
                    <th>Total Users</th>
                    <th></th>
                </tr>
		<?php //$ajaxurl = Router::url(array('controller' => 'ajax','action' => 'changeorgstatus')); 
                    echo $this->Element("rowusersindex", $userdata);
                ?>
            </table>
        </div>
        <div style="text-align: center">
            <?php echo $this->Html->Image("ajax-loader.gif", array("class" => "hiddenloader hidden"));?>
        </div>
    </section>

<div class="TopHeight">&nbsp;</div>
<section class="footer-bg">
    <?php echo $this->Html->link($this->Html->image('addClient.png'),array('controller'=>'users','action'=>'createorg'),array('escape'=>false));  ?>
    <span class="addClient"><?php echo $this->Html->link('Add New Client',array('controller'=>'users','action'=>'createorg')); ?></span>
</section>
<div class="modal fade" id="myModa2_deleteusers" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content" align="center">
            <div class="modal-header">
                <button type="button" class="btn btn-default pull-right close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">X</span></button>
            </div>
            <div class="modal-body">
                <h4 class="modal-title">ARE YOU SURE U WANT TO DELETE?</h4>
                <p>This will delete all data of the User</p>
            </div>
            <div class="modal-footer">
                <div class="text-center"><button type="button" id="deleteclick" onclick="" class="btn btn-primary btn-blue">Confirm</button>
                    <button type="button" class="canceldelete btn btn-primary btn-blue">Cancel</button></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal inactive -->
<input id="pagename" value="indexusers" type="hidden">
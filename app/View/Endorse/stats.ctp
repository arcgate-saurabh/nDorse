<?php
echo $this->Html->script("jquery.knob.min");
$client_image = Router::url('/', true) . 'img/p_pic.png';
if ($profiledata["image"] != "") {
    $user_image = explode("/", $profiledata["image"]);
    if (file_exists(WWW_ROOT . PROFILE_IMAGE_DIR . $user_image[count($user_image) - 1])) {
        $client_image = $user_image[count($user_image) - 1];
        $client_image = Router::url('/', true) . PROFILE_IMAGE_DIR . $client_image;
    }
}
$nodresegivenvalue = $statesdata["endorse_given"];
$ndorsereceivedvalue = $statesdata["endorse_received"] * 10;
$tolalvalue = ($nodresegivenvalue + $ndorsereceivedvalue) % 100;
$badgecount = floor(($nodresegivenvalue + $ndorsereceivedvalue) / 100);
?>

<div class="nDorsement-profile">
    <section>
        <div class="col-md-12">
            <div class="col-md-2 text-center"> 
                <img  alt="" id="client_image" src="<?php echo $client_image; ?>" bigimg="<?php echo $client_image; ?>" width="115" height="115" class="img-circle attached-item1"  > 
            </div>
            <div class="col-md-8">
                <h2 class="u-name"><?php echo ucfirst(trim($profiledata["fname"] . " " . $profiledata["lname"])); ?></h2>
                <h3 class="u-profile">
                    <?php if (isset($profiledata["current_org"]->job_title) && $profiledata["current_org"]->job_title != "") { ?>
                        (<?php echo $profiledata["current_org"]->job_title; ?> )
                    <?php } ?>
                </h3>
                <div class="my-badges">
                    <div class="col-md-3">
                        <h2>My Badges:</h2>
                    </div>

                    <?php if (!empty($statesdata["badges"])) { ?>
                        <div class="col-md-9" id="endorse-badges">
                            <?php
                            $inc = 1;
                            foreach ($statesdata["badges"] as $badgesval) {
                                switch ($badgesval["trophy_id"]) {
                                    case 1:
                                        $tooltipText = 'nDorse Badge: For being a valued team member';
                                        break;
                                    case 2:
                                        $tooltipText = 'nDorse Badge: Top nDorser of the Month Badge';
                                        break;
                                    case 3:
                                        $tooltipText = 'nDorse Badge: Top nDorsed of the Month Badge';
                                        break;
                                    default:
                                        $tooltipText = 'Default Badges';
                                        break;
                                }
                                ?>
                                <div class="badge-count text-center">
                                    <img  alt="" id="client_image" src="<?php echo $badgesval["image"]; ?>" width="60"  data-placement="top" data-toggle="popover" data-trigger="hover" data-content="<?php echo $tooltipText; ?>">
                                    <br />
                                    <?php
                                    if ($inc == 1) {
                                        echo $badgecount;
                                    } else {
                                        echo $badgesval["count"];
                                    }
                                    ?> </div>
                                <?php
                                $inc++;
                            }
                            ?>

                        </div>
                    <?php } ?>

                </div>
            </div>
            <div class="col-md-2 text-center col-xs-12"> 
                <input class="knob" data-width="100" data-fgColor="#00ff00" data-bgColor="#555555" ddialColour="#e9ddc1"  data-thickness=".15"  data-displayInput=true value="<?php echo $tolalvalue; ?>">
            </div>
        </div>
        <div class="clearfix"></div>
    </section>
</div>
<div class="nDorsement-profile">
    <section style="padding:10px;">
        <div class="col-md-12 time-range">
            <div class="pull-left">
                <h3>Select a Time Range</h3>
            </div>
            <div class="pull-right">
                <button class="btn btn-default" id="showdatawithoutdatestas">SHOW ALL</button>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="select-date col-md-12">
            <div class="col-md-3 form-group">
                <label> From</label>

                <?php echo $this->Form->input('startdate', array('placeholder' => 'Start Date', 'type' => 'text', 'id' => 'datepicker_start', 'class' => 'form-control date', 'label' => false)); ?>
            </div>
            <div class="col-md-3 form-group">
                <label> To</label>
                <?php echo $this->Form->input('enddate', array('placeholder' => 'End Date', 'type' => 'text', 'id' => 'datepicker_end', 'class' => 'form-control date', 'label' => false)); ?>
            </div>
            <div class="col-md-6 ">
                <button class="btn btn-default" id="statesearch">SEARCH</button>
                <button class="btn btn-default button-orange" id="showchartendorsment">SHOW CHART</button>

            </div>
        </div>
        <div class="clearfix"></div>
    </section>
    <section id="endorse-stats">
        <div class="ndorse-states">
            <table class="table table-hover table-states">
                <tr>
                    <th>nDorsement Given </th>
                    <th class="text-right"><?php echo $statesdata["endorse_given"]; ?></th>
                </tr>
                <tr>
                    <th>nDorsement Received </th>
                    <th class="text-right"><?php echo $statesdata["endorse_received"]; ?></th>
                </tr>
            </table>
        </div>
        <div class="core-values-div">
            <?php if (!empty($statesdata["core_value"])) { ?>
                <table class="table table-hover table-core-value">
                    <tr>
                        <th colspan="2"><strong>Core Values Collected: </strong></th>
                    </tr>
                    <?php foreach ($statesdata["core_value"] as $coreval) { ?>
                        <tr>
                            <td><?php echo $coreval["name"]; ?></td>
                            <td class="text-right"><?php echo $coreval["value"]; ?> 
                            </td>
                        </tr>
                    <?php } ?>

                </table>
            <?php } ?>
        </div>
    </section>
</div>
</div>
<script>
    $(document).ready(function () {
        $('[data-toggle="popover"]').popover();
        //$('[data-toggle="popover"]').popover('show');  
        $('.attached-item1').on('click', function () {
            var src = $(this).attr('bigimg');
            var img = '<img src="' + src + '" class="img-responsive no-hand"/>';
            var html = '';
            html += img;
            $('#myPhotoModal').modal();
            $('#myPhotoModal').on('shown.bs.modal', function () {
                $('#myPhotoModal .modal-body').html(html);
                //new code
                //$('a.controls').trigger('click');
            })
            $('#myPhotoModal').on('hidden.bs.modal', function () {
                $('#myPhotoModal .modal-body').html('');

            });
        });
    });
</script>
<div class="modal fade" id="myPhotoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom: 0px;">
                <button data-dismiss="modal" class="btn btn-default pull-right close" type="button">×</button>
                <!--                <h3 style="margin-bottom: -20px;">Gallery</h3>-->
            </div>
            <div class="modal-body">                
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
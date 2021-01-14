<?php $path = Router::url('/', true) ; ?>

<div class="row guest-main">
	<div class="div-center">
		<section class="guest-login">
			<form action=""  id="" method="" class="text-center" >
				<div class="like-dis">
					<img src="<?php echo $path ?>/img/nd-like.png">
				</div>
				<h2>Welcome to the Guest nDorsement Page!</h2>
				<div class="form-group company-img">
					<h4>UMCNO</h4>
					<img src="<?php echo $path ?>/img/company01.png" class="">
				</div>

				<div class="form-group"> 
					<div class="input">
						<input  placeholder="First Name" class="form-control" maxlength="60" type="text">
					</div>
				</div>
				<div class="form-group"> 
					<div class="input">
						<input  placeholder="Last Name" class="form-control" maxlength="60" type="text">
					</div>
				</div>
				<div class="form-group"> 
					<div class="input">
						<input  placeholder="Email" class="form-control" maxlength="60" type="text">
					</div>
				</div>

				<div class="form-group ">
					<button class="btn btn-orange btn-block" type="submit" id="">Submit</button>
				</div>

			</form>
		</section>

	</div>
</div>
<div class="MT30"></div>

<div class="row guest-main">
	<div class="div-center">
		<section>
			<form action=""  id="" method="" class="text-center" >
				<h2>Welcome to the Guest nDorsement Page!</h2>
				<div class="form-group text-left MT30">
					<label>Find a Member to nDorse:</label>
					<div class="input text search-icn">
						<input placeholder="Search For Member" class="form-control" value="" type="text" id="">
						<div class="guest-search hidden">
							<div style="position:absolute; right:10px; top:10px;z-index: 999" class="hidden">
								<button class="btn btn-xs btn-warning hand" type="button">CLOSE</button>
							</div>
							<div class="searched-values" > 
								<span class="nDorse-user-icon">
									<img src="<?php echo $path;?>/img/user-icon.png" >
								</span> 
								<span class="txt-ellips">Javed Ahmed Sheikh</span>
								<span class="pull-right org-img">
									<img src="http://api.ndorse.net/app/webroot/uploads/org/small/070317_163230cookmedicallogo.jpg" >
								</span> 
							</div>
							<div class="searched-values" data-endorsementfor="department" data-endorsedid=""> 
								<span class="nDorse-user-icon">
									<img src="<?php echo $path;?>/img/user-icon.png" >
								</span>
								<span class="txt-ellips">Anuj Kalal</span>
								<span class="pull-right org-img">
									<img src="http://api.ndorse.net/app/webroot/uploads/org/small/211016_181954loewslogo.JPG" >
								</span>
							</div>
							<div class="searched-values" data-endorsementfor="department" data-endorsedid=""> 
								<span class="nDorse-user-icon">
									<img src="<?php echo $path;?>/img/user-icon.png" >
								</span> 
								<span class="txt-ellips">Dilbag Singh</span>
								<span class="pull-right org-img">
									<img src="http://api.ndorse.net/app/webroot/uploads/org/small/040816_144013images.jpg" >
								</span>
							</div>
						</div>
					</div>
				</div>

				<div class="text-left">
					<div class="content">
					<label>Core Values: </label>
						<div class="form-group GuestCV ">
							<div class="checkbox core-value">
								<input type="checkbox" class="css-checkbox" id="corevalue_591">
								<label for="corevalue_591" class="css-label">Treated someone with respect </label>
							</div>
							<div class="checkbox core-value">
								<input type="checkbox" class="css-checkbox" id="corevalue_592">
								<label for="corevalue_592" class="css-label">Treated someone with respect </label>
							</div>
							<div class="checkbox core-value">
								<input type="checkbox" class="css-checkbox" id="corevalue_593">
								<label for="corevalue_593" class="css-label">Treated someone with respect </label>
							</div>
							<div class="checkbox core-value">
								<input type="checkbox" class="css-checkbox" id="corevalue_594">
								<label for="corevalue_594" class="css-label">Treated someone with respect </label>
							</div>
							<div class="checkbox core-value">
								<input type="checkbox" class="css-checkbox" id="corevalue_595">
								<label for="corevalue_595" class="css-label">Treated someone with respect </label>
							</div>
						</div>
					</div>
				</div>

				<div class="form-group text-left MT30">
					<label>Add Message: </label>
					<div class="panel panel-default">
						<textarea placeholder="Add Message..." class="add-msg" name="" maxlength="3000"></textarea>
					</div>
					<span class="error" ></span>
					<button class="btn btn-orange btn-block" type="submit" id="">Send</button>
				</div>
			</form>
		</section>
	</div>
</div>

<div class="MT30"></div>
<div class="row guest-main">
	<div class="div-center">
		<section class="text-center cong-msg">
			<h2 class="mb40">Congratulations!</h2>
			<br>
			<h4>You have successfully nDorsed Bryan Phou. You will receive an email notification when your message is received. </h4>
			<br><br>
			<h4>Did you like this guest nDorsement page?</h4>
			<br><br>
			<h4>Your feedback is appreciated.</h4>
			<div class="like-dis MT30">
				<!-- <img src="<?php echo $path;?>/img/nd-like.png" >
				<img src="<?php echo $path;?>/img/nd-dislike.png" >
				<div class="clearfix"></div> -->
				<button class="btn btn-orange btn-block" type="">Exit Guest nDorsement Page</button>
			</div>
		</section>
	</div>
</div>







<div class="modal fade bs-example-modal-lg nDorse-process js_emojis" tabindex="-1" role="dialog" >
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" >Select Stickers</h4>
			</div>
			<div class="modal-body" style="max-height:250px; overflow:auto;">
				<div class="sticker-container">
					<div class="sticker-img js_addSticker" rel="Good_job.gif">
						<img src="http://api.ndorse.net/uploads/endorse/emojis/Good_job.gif" class="attached-item" width="90" alt="">
						<div class="switchbutton"><img class="defaultorg" alt="" src="http://localhost/nDorse_live//img/selected-org.png"></div>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="modal-footer">
				<button class="btn btn-orange pull-left js_selectEmojis">Done</button>
			</div>
		</div>
	</div>
</div>
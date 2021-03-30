<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->                      
<div class="content-page">
	<!-- Start content -->
	<div class="content">
		<div class="container">

			<!-- Page-Title -->
			<div class="row">
				<div class="col-sm-12">
					<h4 class="pull-left page-title"><?=$title?></h4>
					<ol class="breadcrumb pull-right">
						<li><a href="<?=base_url()?>"><?=$site->title?></a></li>
						<li class="active"><?=$title?></li>
					</ol>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default">
						<div class="panel-heading">
							<!--<h3 class="panel-title">Header</h3>-->
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<?php isset($_GET['id'])?$update='?id='.$_GET['id']:$update=null; ?>
									<?=form_open_multipart($this->control.'/'.$page.'/'.$update)?>
										<div class="row" style="margin-bottom: 10px;">
											<div class="col-sm-12">
												<div class="col-sm-12">
													<input type="hidden" name="user_id" value="<?=$preference['user_id']?>" required />
													
													<div class="row" style="margin-bottom:10px;">
														<label class="col-sm-4 control-label text-left">Username</label>
														<div class="col-sm-8">
															<?php $field = 'username' ?>
															<input class="form-control" type="text" name="<?=$field?>" value="<?=isset($preference[$field])?$preference[$field]:set_value($field)?>" required readonly />
															<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														</div> 
													</div>
													<div class="row" style="margin-bottom:10px;">
														<label class="col-sm-4 control-label text-left">Nama</label>
														<div class="col-sm-8">
															<?php $field = 'nama' ?>
															<input class="form-control" type="text" name="<?=$field?>" value="<?=isset($preference[$field])?$preference[$field]:set_value($field)?>" required />
															<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														</div>
													</div>
													<div class="row" style="margin-bottom:10px;">
														<label class="col-sm-4 control-label text-left">Alamat</label>
														<div class="col-sm-8">
															<?php $field = 'alamat' ?>
															<textarea class="form-control" name="<?=$field?>" required ><?=isset($preference[$field])?$preference[$field]:set_value($field)?></textarea>
															<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														</div>
													</div>
													<div class="row" style="margin-bottom:10px;">
														<label class="col-sm-4 control-label text-left">Tanggal Lahir</label>
														<div class="col-sm-8">
															<?php $field = 'tgl_lahir' ?>
															<input class="form-control datepicker_date" type="text" name="<?=$field?>" value="<?=isset($preference[$field])?$preference[$field]:set_value($field)?>" />
															<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														</div>
													</div>
													<div class="row" style="margin-bottom:10px;">
														<label class="col-sm-4 control-label text-left">No. Telp</label>
														<div class="col-sm-8">
															<?php $field = 'nohp' ?>
															<input class="form-control" type="text" name="<?=$field?>" value="<?=isset($preference[$field])?$preference[$field]:set_value($field)?>" />
															<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														</div>
													</div>
													<div class="row" style="margin-bottom:10px;">
														<label class="col-sm-4 control-label text-left">E-mail</label>
														<div class="col-sm-8">
															<?php $field = 'email' ?>
															<input class="form-control" type="email" name="<?=$field?>" value="<?=isset($preference[$field])?$preference[$field]:set_value($field)?>" />
															<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														</div>
													</div>
													<div class="row" style="margin-bottom:10px;">
														<label class="col-sm-4 control-label text-left">Foto</label>
														<div class="col-sm-8">
															<?php $field = 'foto' ?>
															<?php if(isset($preference[$field]) && $preference[$field]!=null){ ?>
																<input type="hidden" name="logo_<?=$field?>" value="<?=$preference[$field]?>">
																<img width="200" src="<?=base_url()?>assets/images/foto/<?=$preference[$field]?>" />
															<?php } ?>
															<input type="file" name="<?=$field?>" id="<?=$field?>" />
															<font color='red'><?php if(isset($error_foto)){ echo $error_foto; } ?></font>
														</div>
													</div>
													<div class="row " style="margin-bottom:10px;">
														<label class="col-sm-4 control-label text-left">Change Password</label>
														<div class="col-sm-8">
															<?php $field = 'password'; ?>
															<input class="form-control" type="password" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" />
															<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														</div>
													</div>
													<div class="row " style="margin-bottom:10px;">
														<label class="col-sm-4 control-label text-left">Confirm Password</label>
														<div class="col-sm-8">
															<?php $field = 'conf_password'; ?>
															<input class="form-control" type="password" id="<?=$field?>" name="<?=$field?>" value="<?=isset($master_data[$field])?$master_data[$field]:set_value($field)?>" />
															<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														</div>
													</div>
												</div>
											</div>
										</div>
										<div class="row" style="margin-bottom: 10px;, margin-top: 5px;">
											<div class="col-sm-12">
												<div class="form-group">
													<div class="col-sm-1 text-left">
														<button class="btn btn-primary" type="submit" onclick="valid_form()" name="save" id="save" ><i class="fa fa-save"></i> Save</button>
													</div>
												</div>
											</div>
										</div>
									<?=form_close()?>
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div> <!-- End Row -->
			
		</div> <!-- container -->
				   
	</div> <!-- content -->

</div>
<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->

<script>
	function valid_form(){
		valid_conf_password();
	}

	function valid_conf_password(){
		var i = document.getElementById("conf_password");
		var password = $('#password').val();
		var conf_password = $('#conf_password').val();

		if (i.validity.valueMissing == true){
			i.setCustomValidity("Don't empty");
		}
		else if (password!=conf_password){
			i.setCustomValidity("Not match");
		}
		else{
			i.setCustomValidity("");
		}
	}
</script>
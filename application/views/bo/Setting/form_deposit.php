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
									<?php isset($_GET['trx'])?$update='?trx='.$_GET['trx']:$update=null; ?>
                                    <?php $preference = json_decode($preference['deposit'], true); ?>
									<?=form_open_multipart($this->control.'/'.$page.'/'.$this->uri->segment(3).'/'.$update)?>
									<?= isset($_GET['trx'])?'<input type="hidden" name="update" value="1" />':''; ?>
										<div class="row" style="margin-bottom: 10px;">
											<div class="col-sm-12">
												<div class="col-sm-12">
													<input type="hidden" name="site_id" value="<?=isset($preference['site_id'])?$preference['site_id']:$this->m_crud->read_data('site', 'max(site_id) as id')[0]['id'] + 1?>" required />	
													
													<div class="row" style="margin-bottom:10px;">
														<label class="col-sm-4 control-label text-left">Minimal Deposit</label>
                                                        <?php $field='minimal'; ?>
														<div class="col-sm-8">
															<input class="form-control" type="text" name="<?=$field?>" value="<?=isset($preference[$field])?$preference[$field]:set_value($field)?>" required />
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
														<button class="btn btn-primary" type="submit" name="save" id="save" ><i class="fa fa-save"></i> Save</button> 
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



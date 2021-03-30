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
									<table id="" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>No</th><th>Action</th><th>Title</th><th>Logo</th><th>Fav Icon</th><th>Meta Key</th><th>Meta Description</th>
											</tr>
										</thead>
										<tbody>
										<?php $no = 0; foreach($preference as $row){ $no++; ?>
											<tr>
												<td><?=$no?></td>
												<td>
													<div class="btn-group">
														<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button>
														<ul class="dropdown-menu" role="menu">
															<li><div class="col-sm-12"><?=anchor($content.'/edit/?trx='.$row['site_id'], '<i class="fa fa-edit"></i> Edit', array('class'=>'btn btn-default col-sm-12'))?></div></li>
															<!--<li class="divider"></li>-->
															<!--<li><div class="col-sm-12"><?=anchor($content.'/delete/?trx='.$row['site_id'], '<i class="fa fa-trash"></i> Delete', array('class'=>'btn btn-default col-sm-12', 'onclick'=>"return confirm('Delete Data?');"))?></div></li>-->
															<!--<button class="btn btn-danger" onclick="hapus('coa', 'coa_id', '<?=$row['coa_id']?>')"><i class="fa fa-trash"></i> Delete</button>-->
														</ul>
													</div>
												</td>
												<td><?=$row['title']?></td>
												<td><img width="200px" src="<?=$this->config->item('url').$row['logo']?>" /></td>
												<td><img width="50px" src="<?=$this->config->item('url').$row['fav_icon']?>" /></td>
												<td><?=$row['meta_key']?></td>
												<td><?=$row['meta_descr']?></td>
											</tr>
										<?php } ?>
										</tbody>
									</table>
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



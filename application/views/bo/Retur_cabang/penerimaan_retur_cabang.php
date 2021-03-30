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
							<div class="row">
								<?=form_open(strtolower($this->control) . '/' . $page, array('role'=>"form", 'class'=>""))?>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Lokasi</label>
											<?php $field = 'lokasi';
											$option = null; $option[''] = '-- Location --';
											//$option['all'] = 'All';
											$data_option = $this->m_crud->read_data('Lokasi', 'Kode, Nama, serial', null, 'Nama asc');
											foreach($data_option as $row){ $option[$row['serial']] = $row['Nama']; }
											echo form_dropdown($field, $option, set_value($field), array('class' => 'select2', 'id'=>$field)); 
											?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label>No. Retur</label>
											<?php $field = 'no_trx'; ?>
											<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=set_value($field)?>" autofocus />	
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-1">
										<label>&nbsp;</label>
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
									</div>
								<?=form_close()?>
							</div>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<table id="" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>No</th>
												<th>Kode Transaksi</th>
												<th>Tanggal</th>
												<th>Lokasi</th>
												<th>Total Item</th>
												<th>Total Qty</th>
												<th>Qty Approval</th>
												<th>Aksi</th>
											</tr>
										</thead>
										<tbody>
											<?php $no = 0; foreach($report as $rows){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td><?=$rows['kd_trx']?></td>
													<td><?=substr($rows['tgl'], 0, 10)?></td>
													<td><?=$rows['lokasi']?></td>
													<td><?=$rows['tot_item']?></td>
													<td><?=(int)$rows['tot_qty']?></td>
													<td><?=(int)$rows['qty_approval']?></td>
													<td>
														<?php if($rows['qty_approval'] < $rows['tot_qty']){ ?><a href="<?=base_url().strtolower($this->control).'/approval_retur_cabang/'.base64_encode($rows['kd_trx'])?>"><button type="button" class="btn btn-primary waves-effect waves-light m-b-5">Approval</button></a><?php } ?>
														<!--<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle waves-effect" data-toggle="dropdown" aria-expanded="false">Dropdown <span class="caret"></span></button>
															<ul class="dropdown-menu" role="menu">
																<li><a href="#">Action</a></li>
																<li><a href="#">Another action</a></li>
																<li><a href="#">Something else here</a></li>
																<li class="divider"></li>
																<li><a href="#">Separated link</a></li>
															</ul>
														</div>-->
													</td>
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




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
							<?=form_open(strtolower($this->control) . '/' . $page, array('role'=>"form", 'class'=>""))?>
								<div class="row">
									<div class="col-sm-3" style="margin-bottom:10px">
										<label>Periode</label>
										<?php $field = 'field-date';?>
										<div id="daterange" style="cursor: pointer;">
											<input type="text" name="<?=$field?>" id="<?=$field?>" class="form-control" style="height: 40px;" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y/m/d")." - ".date("Y/m/d"))?>">
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-sm-3">
										<div class="form-group">
											<label>Lokasi</label>
											<?php $field = 'lokasi';
											$option = null; $option[''] = 'Semua Lokasi';
											//$option['all'] = 'All';
											$data_option = $this->m_crud->read_data('Lokasi', 'Kode, Nama', null, 'Nama asc');
											foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
											echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
											?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label><?=$menu_group['as_group1']?></label>
											<?php $field = 'supplier';
											$option = null; $option[''] = 'Semua '.$menu_group['as_group1'];
											//$option['all'] = 'All';
											$data_option = $this->m_crud->read_data('Group1', 'Kode, Nama', null, 'Nama asc');
											foreach($data_option as $row){ $option[$row['Kode']] = $row['Kode'].' - '.$row['Nama']; }
											echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
											?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Search</label>
											<?php $field = 'any'; ?>
											<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-1">
										<label>&nbsp;</label>
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
									</div>
									<div class="col-sm-1">
										<label>&nbsp;</label>
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="to_excel">Export</button>
									</div>
								</div>
							<?=form_close()?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<table class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>No</th>
												<th>Kode Barang</th>
												<th>Barcode</th>
												<th>Nama Barang</th>
												<th>Stock Awal</th>
												<th>Stock Masuk</th>
												<th>Stock Keluar</th>
												<th>Stock Total</th>
											</tr>
										</thead>
										<tbody>
											<?php $no = 0 + (($this->uri->segment(3)!=null)?(($this->uri->segment(3)-1) * 30):0); $staw = 0; $stma = 0; $stke = 0; $stak = 0; foreach($report as $rows){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td><?=$rows['kd_brg']?></td>
													<td><?=$rows['barcode']?></td>
													<td><?=$rows['nm_brg']?></td>
													<td><?=(int)$rows['stock_awal']?></td>
													<td><?=(int)$rows['stock_masuk']?></td>
													<td><?=(int)$rows['stock_keluar']?></td>
													<td><?=(int)($rows['stock_awal']+$rows['stock_masuk']-$rows['stock_keluar'])?></td>
												</tr>
											<?php
											$staw = $staw + (int)$rows['stock_awal'];
											$stma = $stma + (int)$rows['stock_masuk'];
											$stke = $stke + (int)$rows['stock_keluar'];
											$stak = $stak + (int)($rows['stock_awal']+$rows['stock_masuk']-$rows['stock_keluar']);
											} ?>
										</tbody>
										<tfoot>
										<tr>
											<th colspan="4">TOTAL PER PAGE</th>
											<th><?=$staw?></th>
											<th><?=$stma?></th>
											<th><?=$stke?></th>
											<th><?=$stak?></th>
										</tr>
										<tr>
											<th colspan="4">TOTAL</th>
											<th><?=$tstaw?></th>
											<th><?=$tstma?></th>
											<th><?=$tstke?></th>
											<th><?=$tstak?></th>
										</tr>
										</tfoot>
									</table>
									<div class="pull-right">
										<?php
										echo $this->pagination->create_links();
										?>
									</div>
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
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script>
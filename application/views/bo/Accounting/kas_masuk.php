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
											$data_option = $this->m_crud->read_data('Lokasi', 'Kode, nama_toko Nama', $this->where_lokasi, 'Nama asc');
											foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
											echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
											?>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Jenis Kas</label>
											<?php $field = 'jns_kas';
											$option = null; $option[''] = 'Semua Jenis';
											//$option['all'] = 'All';
											$data_option = $this->m_crud->read_data('master_kas_masuk', 'Kode, Nama', null, 'Nama asc');
											foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
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
												<th>Tanggal</th>
												<th>Kode Transaksi</th>
												<th>Jenis Kas</th>
												<th>Lokasi</th>
												<th>Keterangan</th>
												<th>Operator</th>
												<th>Jumlah</th>
											</tr>
										</thead>
										<tbody>
											<?php
                                            $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0);
                                            $kas = 0;
											foreach($report as $row){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td><?=substr($row['tgl'],0,10)?></td>
													<td><?=$row['kd_trx']?></td>
													<td><?=$row['nama_kas']?></td>
													<td><?=$row['Lokasi']?></td>
													<td><?=$row['keterangan']?></td>
													<td><?=$row['nama']?></td>
													<td class="text-right"><?=number_format($row['jumlah'])?></td>
												</tr>
											<?php
											$kas = $kas + $row['jumlah'];
											} ?>
										</tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="7">TOTAL PER PAGE</th>
                                            <th class="text-right"><?=number_format($kas)?></th>
                                        </tr>
                                        <tr>
                                            <th colspan="7">TOTAL</th>
                                            <th class="text-right"><?=number_format($tkas)?></th>
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
</script
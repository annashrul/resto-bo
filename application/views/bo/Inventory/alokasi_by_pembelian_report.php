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
											<label>Search</label>
											<?php $field = 'any'; ?>
											<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" placeholder="Kode Transaksi/No Nota/Operator" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
									</div>
									<div class="col-sm-1">
										<label>&nbsp;</label>
										<button type="submit" class="btn btn-primary waves-effect waves-light" name="search">Search</button>
									</div>
									<div class="col-sm-1">
										&nbsp;<label>&nbsp;</label>
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
												<th>No. Alokasi</th>
												<th>No Nota</th>
												<th>Lokasi</th>
												<th>Operator</th>
												<th>Pilihan</th>
											</tr>
										</thead>
										<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 20):0); foreach($report as $row){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td><?=substr($row['tgl_beli'],0,10)?></td>
													<td><?=$row['no_faktur_beli']?></td>
													<td><?=$row['noNota']?></td>
													<td><?=$row['Lokasi']?></td>
													<td><?=$this->m_website->get_nama_user($row['Operator'])?></td>
													<td class="text-center">
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
															<ul class="dropdown-menu" role="menu">
																<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
																<li><a href="#" onclick="re_print('<?=$row['no_faktur_beli']?>', download_pdf)"><i class="md md-get-app"></i> Download</a></li>
																<li><a href="#" onclick="re_print('<?=$row['no_faktur_beli']?>', cetak_pdf)" target="_blank"><i class="md md-print"></i> to PDF</a></li>
																<li><a href="#" onclick="re_print('<?=$row['no_faktur_beli']?>', cetak, 'print_alokasi')"><i class="md md-print"></i> Print</a></li>
																<!--<li class="divider"></li>-->
															</ul>
														</div>
													</td>
												</tr>
											<?php } ?>
										</tbody>
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

<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 20):0); foreach($report as $row){ $i++; ?>
<div id="<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-3"><b>Tanggal</b></div><div class="col-sm-9"><b> : </b><?=substr($row['tgl_beli'],0,10)?></div>
						<div class="col-sm-3"><b>No. Alokasi</b></div><div class="col-sm-9"><b> : </b><?=$row['no_faktur_beli']?></div>
						<div class="col-sm-3"><b>Lokasi</b></div><div class="col-sm-9"><b> : </b><?=$row['Lokasi']?></div>
					</div>
					<div class="col-sm-2"></div>
					<div class="col-sm-4">
						<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?=$this->m_website->get_nama_user($row['Operator'])?></div>
					</div>
				</div>	
				<hr/>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<table id="datatable" class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>No</th>
									<th>Kode Barang</th>
									<th>Barcode</th>
									<th>Nama Barang</th>
									<th>Qty</th>
									<th>Harga</th>
								</tr>
							</thead>
							<tbody>
								<?php $no = 0;
								$total = 0;
								$detail = $this->m_crud->join_data('det_beli as db', 'kode_barang, barcode, nm_brg, jumlah_beli, hrg_jual_1, isnull(db.jumlah_bonus, 0) jumlah_bonus, isnull(dr.jml,0) jumlah_retur', array(array('table'=>'barang as br', 'type'=>'LEFT'), array('table'=>'Master_Retur_Beli mr', 'type'=>'LEFT'), array('table'=>'Det_Retur_Beli dr', 'type'=>'LEFT')), array('kode_barang = kd_brg','db.no_faktur_beli=mr.no_beli','dr.No_Retur=mr.No_Retur AND dr.kd_brg=db.kode_barang'), "no_faktur_beli = '".$row['no_faktur_beli']."'");
								foreach($detail as $rows){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kode_barang']?></td>
										<td><?=$rows['barcode']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td class="text-center"><?=(int)$rows['jumlah_beli']-(int)$rows['jumlah_retur'].(((int)$rows['jumlah_bonus']>0)?" + ".(int)$rows['jumlah_bonus']:"")?></td>
										<td style="text-align:right;"><?=number_format($rows['hrg_jual_1'])?></td>
									</tr>
								<?php
								$total = $total + (int)$rows['jumlah_beli']-(int)$rows['jumlah_retur']+(int)$rows['jumlah_bonus'];
								} ?>
							</tbody>
							<tfoot>
							<tr>
								<th class="text-left" colspan="4">TOTAL</th>
								<th class="text-center"><?=$total?></th>
								<th></th>
							</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
				<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['no_faktur_beli'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="print_alokasi<?=$row['no_faktur_beli']?>" class="hidden print-nota">
    <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['no_faktur_beli']?>">
	<table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
		<thead>
		<tr>
			<td colspan="8" style="text-align: center">Alokasi By Pembelian (<?=$row['no_faktur_beli']?>)</td>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td width="2%"></td>
			<td width="20%"></td>
			<td width="2%"></td>
			<td width="28%"></td>

			<td width="10%"></td>
			<td width="12%"></td>
			<td width="2%"></td>
			<td width="25%"></td>
		</tr>
		<tr>
			<td></td>
			<td>Tanggal</td>
			<td>:</td>
			<td><?=substr($row['tgl_beli'],0,10)?></td>

			<td></td>
			<td>Operator</td>
			<td>:</td>
			<td><?=$this->m_website->get_nama_user($row['Operator'])?></td>
		</tr>
		<tr>
			<th></th>
			<td>Lokasi</td>
			<td>:</td>
			<td><?=$row['Lokasi']?></td>

			<td></td>
			<td>Nota Supplier</td>
			<td>:</td>
			<td><?=$row['noNota']?></td>
		</tr>
		</tbody>
	</table>

	<table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
		<thead>
		<tr>
			<td style="width: 5%; border-bottom: solid; border-width: thin; padding-left: 5pt">No</td>
			<td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Kode Barang</td>
			<td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Barcode</td>
			<td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt">Nama Barang</td>
			<td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-center">Qty</td>
			<td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-right">Harga</td>
		</tr>
		</thead>
		<tbody>
		<?php $no = 0;
		foreach($detail as $rows){ $no++; ?>
			<tr>
				<td><?=$no?></td>
				<td><?=$rows['kode_barang']?></td>
				<td><?=$rows['barcode']?></td>
				<td><?=$rows['nm_brg']?></td>
				<td class="text-center"><?=(int)$rows['jumlah_beli']-(int)$rows['jumlah_retur'].(((int)$rows['jumlah_bonus']>0)?" + ".(int)$rows['jumlah_bonus']:"")?></td>
				<td class="text-right"><?=number_format($rows['hrg_jual_1'])?></td>
			</tr>
		<?php } ?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="4" style="border-top: solid; border-width: thin">TOTAL</td>
			<td class="text-center" style="border-top: solid; border-width: thin"><?=$total?></td>
			<td style="border-top: solid; border-width: thin"></td>
		</tr>
		</tfoot>
	</table>

	<table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
		<thead>
		<tr>
			<th style="border-top: solid; border-width: thin;" width="33%"></th>
			<th style="border-top: solid; border-width: thin;" width="33%"></th>
			<th style="border-top: solid; border-width: thin;" width="33%"></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td style="text-align:center;">
				<br/>Pengirim<br/><br/><br/>_____________
			</td>
			<td style="text-align:center;">
				<br/>Mengetahui<br/><br/><br/>_____________
			</td>
			<td style="text-align:center;">
				<br/>Penerima<br/><br/><br/>_____________
			</td>
		</tr>
		</tbody>
	</table>
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['no_faktur_beli'], 'reprint')?></span>
</div>
<?php } ?>

<script>
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script
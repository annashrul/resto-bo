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
												<th>No. Adjustment</th>
												<th>Lokasi</th>
												<th>Keterangan</th>
												<th>Operator</th>
												<th>Pilihan</th>
											</tr>
										</thead>
										<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0); foreach($report as $row){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td><?=substr($row['tgl'],0,10)?></td>
													<td><?=$row['kd_trx']?></td>
													<td><?=$row['lokasi']?></td>
													<td><?=$row['keterangan']?></td>
													<td><?=$row['nama']?></td>
													<td class="text-center">
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
															<ul class="dropdown-menu" role="menu">
																<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['kd_trx']?>', download_pdf)"><i class="md md-get-app"></i> Download</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['kd_trx']?>', cetak_pdf)" target="_blank"><i class="md md-print"></i> to PDF</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['kd_trx']?>', cetak, 'print_adjusment')"><i class="md md-print"></i> Print</a></li>
                                                                <li><a href="#" id="delete" onclick="delete_trans('<?=$row['kd_trx']?>')"><i class="md md-close"></i> Delete</a></li>
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

<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0); foreach($report as $row){ $i++; ?>
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
						<div class="col-sm-3"><b>Tanggal</b></div><div class="col-sm-9"><b> : </b><?=substr($row['tgl'],0,10)?></div>
						<div class="col-sm-3"><b>No. Adjust</b></div><div class="col-sm-9"><b> : </b><?=$row['kd_trx']?></div>
						<div class="col-sm-3"><b>Lokasi</b></div><div class="col-sm-9"><b> : </b><?=$row['lokasi']?></div>
					</div>
					<div class="col-sm-2"></div>
					<div class="col-sm-4">
						<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?=$row['nama']?></div>
						<div class="col-sm-4"><b>Keterangan</b></div><div class="col-sm-8"><b> : </b><?=$row['keterangan']?></div>
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
									<th>Stock Terakhir</th>
									<th>Jenis</th>
									<th>Qty Adjust</th>
									<th>Saldo Stock</th>
								</tr>
							</thead>
							<tbody>
								<?php $no = 0;
								$total = 0;
								$detail = $this->m_crud->join_data('det_adjust da', 'br.kd_brg, br.barcode, br.nm_brg, da.status, isnull(da.stock_terakhir, 0) stock_terakhir, da.qty_adjust, da.saldo_stock', array(array('table'=>'barang br', 'type'=>'LEFT')), array('da.kd_brg = br.kd_brg'), "da.kd_trx = '".$row['kd_trx']."'");
								foreach($detail as $rows){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=$rows['barcode']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td class="text-center"><?=(int)$rows['stock_terakhir']?></td>
                                        <td><?=$rows['status']?></td>
                                        <td class="text-center"><?=(int)$rows['qty_adjust']?></td>
                                        <td class="text-center"><?=(int)$rows['saldo_stock']?></td>
									</tr>
								<?php
								} ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
				<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['kd_trx'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="print_adjusment<?=$row['kd_trx']?>" class="hidden print-nota">
    <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['kd_trx']?>">
	<table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
		<thead>
		<tr>
			<td colspan="8" style="text-align: center">Adjustment Stock (<?=$row['kd_trx']?>)</td>
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
			<td><?=substr($row['tgl'],0,10)?></td>

			<td></td>
			<td>Operator</td>
			<td>:</td>
			<td><?=$row['nama']?></td>
		</tr>
		<tr>
			<th></th>
			<td>Lokasi</td>
			<td>:</td>
			<td><?=$row['lokasi']?></td>

            <td></td>
            <td>Keterangan</td>
            <td>:</td>
			<td><?=$row['keterangan']?></td>
		</tr>
		</tbody>
	</table>

	<table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
		<thead>
		<tr>
			<td style="width: 5%; border-bottom: solid; border-width: thin; padding-left: 5pt">No</td>
			<td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Kode Barang</td>
			<td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Barcode</td>
			<td style="width: 30%; border-bottom: solid; border-width: thin; padding-left: 5pt">Nama Barang</td>
            <td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-center">Stock Terakhir</td>
            <td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt">Jenis</td>
            <td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-center">Qty Adjust</td>
            <td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-center">Saldo Stock</td>
		</tr>
		</thead>
		<tbody>
		<?php $no = 0;
		foreach($detail as $rows){ $no++; ?>
			<tr>
				<td><?=$no?></td>
				<td><?=$rows['kd_brg']?></td>
				<td><?=$rows['barcode']?></td>
				<td><?=$rows['nm_brg']?></td>
				<td class="text-center"><?=(int)$rows['stock_terakhir']?></td>
                <td><?=$rows['status']?></td>
                <td class="text-center"><?=(int)$rows['qty_adjust']?></td>
                <td class="text-center"><?=(int)$rows['saldo_stock']?></td>
            </tr>
		<?php } ?>
		</tbody>
	</table>

	<table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
		<thead>
		<tr>
			<th style="border-top: solid; border-width: thin;" width="50%"></th>
			<th style="border-top: solid; border-width: thin;" width="50%"></th>
		</tr>
		</thead>
		<tbody>
		<tr>
			<td style="text-align:center;">
				<br/>Operator<br/><br/><br/>_____________
			</td>
			<td style="text-align:center;">
				<br/>Mengetahui<br/><br/><br/>_____________
			</td>
		</tr>
		</tbody>
	</table>
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['kd_trx'], 'reprint')?></span>
</div>
<?php } ?>

<script>
    function delete_trans(kode) {
        if (confirm('Akan menghapus data?')) {
            var table_ = ['adjust', 'det_adjust', 'Kartu_stock'];
            var condition_ = ['kd_trx=\''+kode+'\'','kd_trx=\''+kode+'\'','kd_trx=\''+kode+'\''];
			
			hapus_otorisasi({param:'', kode:btoa(kode), activity:'Hapus Adjustment', table:table_, condition:condition_}, delete_transaksi);
        }
    }
	
	function delete_transaksi(id, res) {
	    if (res == true) {
			$.ajax({
				url: "<?php echo base_url() . 'site/delete_ajax_trx' ?>",
				type: "POST",
				data: {table: id.table, condition: id.condition},
				success: function (res) {
					if (res == true) {
						alert("Data berhasil dihapus!");
					} else {
						alert("Data gagal dihapus!");
					}
					window.location = window.location.href.replace('#','');
				}
			});
        }
    }
	
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script
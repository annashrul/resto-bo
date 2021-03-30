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
								<div class="col-sm-3">
									<div class="form-group">
										<label>Lokasi</label>
										<?php $field = 'lokasi';
										$option = null; $option[''] = 'Semua Lokasi';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Lokasi', 'Kode, Nama', "kode in (".$lokasi_in.")", 'Nama asc');
										foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Search</label>
										<?php $field = 'any'; ?>
										<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" placeholder="No PO/Supplier/Operator" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
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
											<th>No. PO</th>
											<th>Nama Supplier</th>
											<th>Lokasi</th>
											<th>Operator</th>
											<th>Status</th>
											<th>Pilihan</th>
										</tr>
										</thead>
										<tbody>
										<?php $no = 0 + (($this->uri->segment(3)!=null)?(($this->uri->segment(3)-1) * 15):0); foreach($report as $row){ $no++; ?>
											<tr>
												<td><?=$no?></td>
												<td><?=substr($row['tgl_order'],0,10)?></td>
												<td><?=$row['no_order']?></td>
												<td><?=$row['nama_supplier']?></td>
												<td><?=$row['lokasi']?></td>
												<td><?=$this->m_website->get_nama_user($row['kd_kasir'])?></td>
												<td><?php if ($row['status'] == 0){
														echo '<div class="panel panel-primary" style="margin-bottom: -1px"><div class="panel-heading text-center">Processing</div></div>';
													}else{
														echo '<div class="panel panel-success" style="margin-bottom: -1px"><div class="panel-heading text-center" style="color: white">Ordered</div></div>';
													}?>
												</td>
												<td class="text-center">
													<div class="btn-group">
														<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
														<ul class="dropdown-menu" role="menu">
															<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
                                                            <li><a href="#" onclick="re_print('<?=$row['no_order']?>', download_pdf)"><i class="md md-get-app"></i> Download</a></li>
                                                            <li><a href="#" onclick="re_print('<?=$row['no_order']?>', cetak_pdf)"><i class="md md-print"></i> to PDF</a></li>
															<!--<li><a href="<?=base_url().'cetak/barcode_barang/'.base64_encode($row['no_order']).'/'.base64_encode('p_order')?>" target="_blank"><i class="md md-print"></i> Barcode Barang</a></li>-->
                                                            <?php if ($row['status'] == 0){ ?>
															<li><a href="#" onclick="edit_otorisasi({param:'href_target_blank', kode:'<?=base64_encode($row['no_order'])?>', activity:'Edit Purchase Order Cabang'}, edit_trx)"><i class="md md-edit"></i> Edit</a></li>
															<li><a href="#" id="delete" onclick="delete_trans('<?=$row['no_order']?>')"><i class="md md-close"></i> Delete</a></li>
                                                            <?php } ?>

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

<?php $i = 0 + (($this->uri->segment(3)!=null)?(($this->uri->segment(3)-1) * 15):0); foreach($report as $row){ $i++; ?>
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
                            <div class="col-sm-3"><b>No. PO</b></div><div class="col-sm-9"><b> : </b><?=$row['no_order']?></div>
                            <div class="col-sm-3"><b>Tgl PO</b></div><div class="col-sm-9"><b> : </b><?=substr($row['tgl_order'],0,10)?></div>
                            <div class="col-sm-3"><b>Lokasi</b></div><div class="col-sm-9"><b> : </b><?=$row['lokasi']?></div>
                            <div class="col-sm-3"><b>Operator</b></div><div class="col-sm-9"><b> : </b><?=$this->m_website->get_nama_user($row['kd_kasir'])?></div>
						</div>
						<div class="col-sm-1"></div>
						<div class="col-sm-5">
							<div class="col-sm-4"><b>Supplier</b></div><div class="col-sm-8"><b> : </b><?=$row['nama_supplier']?></div>
							<div class="col-sm-4"><b>Alamat</b></div><div class="col-sm-8"><b> : </b><?=$row['alamat_supplier']?></div>
							<div class="col-sm-4"><b>Telepon</b></div><div class="col-sm-8"><b> : </b><?=$row['telp_supplier']?></div>
							<div class="col-sm-4"><b>Keterangan</b></div><div class="col-sm-8"><b> : </b><?=$row['catatan']?></div>
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
									<th>Artikel</th>
									<th>Satuan</th>
									<th>Qty</th>
									<th>Harga Beli</th>
									<th>Harga Jual</th>
								</tr>
								</thead>
								<tbody>
								<?php $no = 0;
								$detail = $this->m_crud->join_data('det_order dp', 'dp.kd_brg kode_barang, br.barcode, br.nm_brg, dp.qty jumlah_beli, dp.hrg_beli harga_beli, dp.hrg_jual harga_jual, br.satuan, br.deskripsi', 'barang as br', 'dp.kd_brg = br.kd_brg', "dp.no_order = '".$row['no_order']."'");
								foreach($detail as $rows){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kode_barang']?></td>
										<td><?=$rows['barcode']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td><?=$rows['deskripsi']?></td>
										<td><?=$rows['satuan']?></td>
										<td><?=(int)$rows['jumlah_beli']?></td>
										<td style="text-align:right;"><?=number_format($rows['harga_beli'])?></td>
										<td style="text-align:right;"><?=number_format($rows['harga_jual'])?></td>
									</tr>
								<?php } ?>
								</tbody>
                                <!--<tfoot>
                                <tr>
                                    <td colspan="8"><?=$row['catatan']?></td>
                                </tr>
                                </tfoot>-->
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
					<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['no_order'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } ?>

<script>
	function edit_trx(id, res) {
        if (res == true) {
			if (id.param == 'href_target_blank') {
                window.open("<?=base_url().'pembelian/po_by_cabang/?trx='?>" + id.kode);
            } else if(id.param == 'href') {
                window.location = "<?=base_url().'pembelian/po_by_cabang/?trx='?>" + id.kode;
            }
        }
    }
	
	function delete_trans(kode) {
		if (confirm('Akan menghapus data?')) {
			var table_ = ['master_order', 'det_order'];
			var condition_ = ['no_order=\''+kode+'\'','no_order=\''+kode+'\''];
			
			hapus_otorisasi({param:'', kode:btoa(kode), activity:'Hapus Purchase Order Cabang', table:table_, condition:condition_}, delete_transaksi);
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
</script>

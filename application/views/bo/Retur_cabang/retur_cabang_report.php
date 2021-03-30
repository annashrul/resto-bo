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
							<div class="row" style="margin-bottom: 10px">
								<div class="col-md-3">
									<label>Periode</label>
									<?php $field = 'field-date';?>
									<div id="daterange" style="cursor: pointer;">
										<input type="text" name="<?=$field?>" id="<?=$field?>" class="form-control" style="height: 40px;" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y/m/d")." - ".date("Y/m/d"))?>">
									</div>
								</div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Kondisi</label>
                                        <?php $field = 'kondisi';
                                        $kondisi = json_decode($menu_group['status_barang'], true);
                                        $option = null; $option[''] = 'Semua Kondisi';
                                        for ($x = 0; $x < count($kondisi['status_barang_ho']); $x++) {
                                            $option[$kondisi['status_barang_ho'][$x]['status']] = $kondisi['status_barang_ho'][$x]['status'];
                                        }
                                        echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
                                        ?>
                                    </div>
                                </div>
							</div>
							<div class="row">
								<div class="col-sm-3">
									<div class="form-group">
										<label>Lokasi Cabang</label>
										<?php $field = 'lokasi';
										$option = null; $option[''] = 'Semua Lokasi';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Lokasi', 'Kode, Nama, serial', null, 'Nama asc');
										foreach($data_option as $row){ $option[$row['serial']] = $row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Status</label>
										<?php $field = 'tipe';
										$option = null; $option[''] = 'Semua Status';
										$option['1'] = 'Approved';
										$option['2'] = 'Approved In Part';
										$option['0'] = 'Approval Process';
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
											<th style="width: 10px">No</th>
											<th>Kode Transaksi</th>
											<th>Tanggal</th>
											<th>Lokasi</th>
											<th>Total Item</th>
											<th>Total Qty</th>
											<th>Qty Approval</th>
											<th>Qty Selisih</th>
											<th>Kondisi</th>
											<th>Status</th>
											<th>Pilihan</th>
										</tr>
										</thead>
										<tbody>
										<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); $tp = 0;
										$ti = 0;
										$tq = 0;
										$qa = 0;
										$sl = 0;
										foreach($report as $row){
											$selisih_ = (int)$row['tot_qty']-(int)$row['qty_approval'];
											$no++; ?>
											<tr>
												<td><?=$no?></td>
												<td><?=$row['kd_trx']?></td>
												<td><?=substr($row['tgl'], 0, 10)?></td>
												<td><?=$row['lokasi']?></td>
												<td><?=$row['tot_item']?></td>
												<td><?=(int)$row['tot_qty']?></td>
												<td><?=(int)$row['qty_approval']?></td>
												<td><?=$selisih_?></td>
												<td><?php print_r(json_decode($row['keterangan2'], true)[0]['status']) ?></td>
												<td><?php if($selisih_ == 0){echo 'Approved';}else if((int)$row['qty_approval'] > 0 && $selisih_ != 0){echo 'Approved In Part';}else{echo 'Approval Process';}?></td>
												<td class="text-center">
													<div class="btn-group">
														<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
														<ul class="dropdown-menu" role="menu">
															<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
                                                            <li><a href="#" onclick="re_print('<?=$row['kd_trx']?>', download_pdf)"><i class="md md-get-app"></i> Download</a></li>
                                                            <li><a href="#" onclick="re_print('<?=$row['kd_trx']?>', cetak_pdf)"><i class="md md-print"></i> to PDF</a></li>
															<!--<li class="divider"></li>-->
														</ul>
													</div>
												</td>
											</tr>
										<?php
										$ti = $ti + $row['tot_item'];
										$tq = $tq + $row['tot_qty'];
										$qa = $qa + $row['qty_approval'];
										$sl = $sl + $selisih_;
										} ?>
										</tbody>
										<tfoot>
										<tr>
											<th colspan="4">TOTAL PER PAGE</th>
											<th><?=$ti?></th>
											<th><?=$tq?></th>
											<th><?=$qa?></th>
											<th><?=$sl?></th>
											<th></th>
											<th></th>
											<th></th>
										</tr>
										<tr>
											<th colspan="4">TOTAL</th>
											<th><?=$tti?></th>
											<th><?=$ttq?></th>
											<th><?=$tqa?></th>
											<th><?=$tsl?></th>
											<th></th>
											<th></th>
											<th></th>
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

<!--Detail-->
<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); foreach($report as $row){
    $keterangan = json_decode($row['keterangan2'], true);
    $i++; ?>
	<div id="<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-full">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-6">
							<div class="col-sm-4"><b>Tanggal</b></div><div class="col-sm-8"><b> : </b><?=substr($row['tgl'],0,10)?></div>
							<div class="col-sm-4"><b>Kode Transaksi</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_trx']?></div>
							<div class="col-sm-4"><b>Lokasi Cabang</b></div><div class="col-sm-8"><b> : </b><?=$row['lokasi']?></div>
						</div>
                        <div class="col-sm-6">
                            <div class="col-sm-4"><b>Kondisi</b></div><div class="col-sm-8"><b> : </b><?=$keterangan[0]['status']?></div>
                            <div class="col-sm-4"><b>Informasi</b></div><div class="col-sm-8"><b> : </b><?=$keterangan[0]['information']?></div>
                        </div>
					</div>
					<hr/>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<table class="table table-striped table-bordered">
								<thead>
								<tr>
									<th>No</th>
									<th>Kode Barang</th>
									<th>Barcode</th>
									<th>Nama Barang</th>
									<th>Qty</th>
									<th>Qty Approval</th>
									<th>Selisih</th>
									<th>Status</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$no = 0;
								$dqt = 0;
								$dqa = 0;
								$dsl = 0;
								$where = "kd_trx = '".$row['kd_trx']."' and keterangan = 'Retur Non Approval' and lokasi = 'retur'";
								$detail = $this->m_crud->join_data('kartu_stock as ks', "ks.kd_brg, br.barcode, stock_in as qty, br.nm_brg, ks.hrg_beli, isnull((select sum(stock_out) from kartu_stock where kartu_stock.kd_brg = ks.kd_brg and keterangan = 'Retur Approval ".$row['kd_trx']."'),0) as qty_approval", 'barang as br', 'ks.kd_brg = br.kd_brg', $where);
								foreach($detail as $rows){
									$no++;
									$selisih = (int) $rows['qty']-(int) $rows['qty_approval'];
									?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=$rows['barcode']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td><?=(int) $rows['qty']?></td>
										<td><?=(int) $rows['qty_approval']?></td>
										<td><?=$selisih?></td>
										<td><?php if($selisih == 0){echo 'Approved';}else if((int)$rows['qty_approval'] > 0 && $selisih != 0){echo 'Approved In Part';}else{echo 'Approval Process';}?></td>
									</tr>
								<?php
								$dqt = $dqt + $rows['qty'];
								$dqa = $dqa + $rows['qty_approval'];
								$dsl = $dsl + $selisih;
								} ?>
								</tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="4">TOTAL</th>
									<th><?=$dqt?></th>
									<th><?=$dqa?></th>
									<th><?=$dsl?></th>
									<th></th>
                                </tr>
                                </tfoot>
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
<?php } ?>

<script>
	function delete_trans(kode) {
		if (confirm('Akan menghapus data?')) {
			var table_ = ['kartu_stock'];
			var condition_ = ['kd_trx=\''+kode+'\''];

			$.ajax({
				url: "<?php echo base_url().'site/delete_ajax_trx' ?>",
				type: "POST",
				data: {table:table_, condition:condition_},
				success: function (res) {
					if (res == true) {
						alert("Data berhasil dihapus!");
						location.reload();
					} else {
						alert("Data gagal dihapus!");
						location.reload();
					}
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
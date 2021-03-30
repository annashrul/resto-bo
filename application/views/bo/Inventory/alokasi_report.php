<style>
    @media print {
        .print-nota {
            display: block;
            font-family: "Calibri" !important;
            margin: 0;
        }

        @page {
            size: 21.59cm 13.97cm;
        }

    }
</style>
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
											<label>Lokasi Tujuan</label>
											<?php $field = 'lokasi';
											$option = null; $option[''] = 'Semua Lokasi';
											//$option['all'] = 'All';
											$data_option = $this->m_crud->read_data('Lokasi', 'Kode, nama_toko Nama', null, 'Nama asc');
											foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
											echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
											?>
										</div>
									</div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <?php $field = 'status';
                                            $option = null;
                                            $option[''] = 'Semua Status';
                                            $option['0'] = 'Packing';
                                            $option['1'] = 'Packed';
                                            $option['2'] = 'Received';
                                            echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
                                            ?>
                                        </div>
                                    </div>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Search</label>
											<?php $field = 'any'; ?>
											<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" placeholder="Alokasi/Delivery Note/Operator" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
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
												<th>Jenis</th>
												<th>Nota Delivery Note</th>
												<th>Lokasi Asal</th>
												<th>Lokasi Tujuan</th>
												<th>Operator</th>
												<th>Status</th>
												<th>Pilihan</th>
											</tr>
										</thead>
										<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0);
											foreach($report as $row){
												$no++;
												$this->m_export_file->create_barcode(array("code"=>$row['no_faktur_mutasi'], "file"=>$row['no_faktur_mutasi']));
												?>
												<tr>
													<td><?=$no?></td>
													<td><?=substr($row['tgl_mutasi'],0,10)?></td>
													<td><?=$row['no_faktur_mutasi']?></td>
													<td><?=(substr($row['no_faktur_mutasi'], 0, 2)=='MU'?'Mutasi':'Branch')?></td>
													<td><?=$row['no_faktur_beli']?></td>
													<td><?=$row['kd_lokasi_1']?></td>
													<td><?=$row['kd_lokasi_2']?></td>
													<td><?=$this->m_website->get_nama_user($row['kd_kasir'])?></td>
													<td><?php if ($row['status'] == 0) {
															echo '<div class="panel panel-warning" style="margin-bottom: -1px"><div class="panel-heading text-center">Packing</div></div>';
														} else if ($row['status'] == 1) {
															echo '<div class="panel panel-primary" style="margin-bottom: -1px"><div class="panel-heading text-center">Packed</div></div>';
														} else {
															echo '<div class="panel panel-success" style="margin-bottom: -1px"><div class="panel-heading text-center" style="color: white">Received</div></div>';
														}?>
													</td>
													<td class="text-center">
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
															<ul class="dropdown-menu" role="menu">
																<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
																<li><a href="#" data-toggle="modal" data-target="#packing-<?=$no?>"><i class="md md-visibility"></i> Detail Packing</a></li>
																<li><a href="#" onclick="re_print('<?=$row['no_faktur_mutasi']?>', download_pdf)"><i class="md md-get-app"></i> Download</a></li>
																<li><a href="#" onclick="re_print('<?=$row['no_faktur_mutasi']?>', cetak_pdf)"><i class="md md-print"></i> to PDF</a></li>
																<li><a href="#" onclick="re_print('<?=$row['no_faktur_mutasi']?>', cetak, 'print_alokasi')"><i class="md md-print"></i> Print</a></li>
                                                                <li><a href="<?=base_url().'cetak/barcode_barang/'.base64_encode($row['no_faktur_mutasi']).'/'.base64_encode('alokasi')?>" target="_blank"><i class="md md-print"></i> Barcode Barang</a></li>
                                                                <?php if ($row['status'] == 0) {
																	echo '<li><a href="#" onclick="edit_otorisasi({param:\'href_target_blank\', kode:\''.base64_encode($row['no_faktur_mutasi']).'\', activity:\'Edit Alokasi\'}, edit_trx)" ><i class="md md-edit" ></i> Edit</a ></li>';
                                                                } ?>
																<li><a href="#" id="delete" onclick="delete_trans('<?=$row['no_faktur_mutasi']?>')"><i class="md md-close"></i> Delete</a></li>

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

<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); foreach($report as $row){ $i++; ?>
<div id="print_alokasi<?=$row['no_faktur_mutasi']?>" class="hidden print-nota">
    <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['no_faktur_mutasi']?>">
    <table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
        <thead>
        <tr>
            <td colspan="8" style="text-align: center">Alokasi Barang (<?=$row['no_faktur_mutasi']?>)</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td width="2%"></td>
            <td width="20%"></td>
            <td width="2%"></td>
            <td width="28%"></td>

            <td width="6%"></td>
            <td width="12%"></td>
            <td width="2%"></td>
            <td width="29%"></td>
        </tr>
        <tr>
            <td></td>
            <td>Tanggal</td>
            <td>:</td>
            <td><?=substr($row['tgl_mutasi'],0,10)?></td>

            <td></td>
            <td>Operator</td>
            <td>:</td>
            <td><?=$this->m_website->get_nama_user($row['kd_kasir'])?></td>
        </tr>
        <tr>
            <th></th>
            <td>Lokasi Asal</td>
            <td>:</td>
            <td><?=$row['kd_lokasi_1']?></td>

            <td></td>
            <td>Jenis Transaksi</td>
            <td>:</td>
            <td><?=(substr($row['no_faktur_mutasi'], 0, 2)=='MU'?'Mutasi':'Branch')?></td>
        </tr>
        <tr>
            <th></th>
            <td>Lokasi Tujuan</td>
            <td>:</td>
            <td><?=$row['kd_lokasi_2']?></td>

            <td></td>
            <td>Delivery Note</td>
            <td>:</td>
            <td><?=$row['no_faktur_beli']?></td>
        </tr>
        </tbody>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
        <thead>
        <tr>
            <td style="width: 5%; border-bottom: solid; border-width: thin; padding-left: 5pt">No</td>
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Kode Barang</td>
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Barcode/<?=substr($menu_group['as_deskripsi'],0,3)?></td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt">Nama Barang</td>
            <td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-center">Qty</td>
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-right">Harga</td>
        </tr>
        </thead>
        <tbody>
        <?php $no = 0; $total = 0;
        $detail = $this->m_crud->join_data('Det_Mutasi as dm', 'br.kd_brg, br.barcode, br.Deskripsi, br.nm_brg, dm.qty, dm.hrg_jual', 'barang as br', 'br.kd_brg = dm.kd_brg', "dm.no_faktur_mutasi = '".$row['no_faktur_mutasi']."'");
        foreach($detail as $rows){ $no++; ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$rows['kd_brg']?></td>
                <td><?=($rows['barcode']==$rows['kd_brg']?$rows['Deskripsi']:$rows['barcode'])?></td>
                <td><?=$rows['nm_brg']?></td>
                <td class="text-center"><?=(int)$rows['qty']?></td>
                <td class="text-right"><?=number_format($rows['hrg_jual'])?></td>
            </tr>
        <?php
        $total = $total + (int)$rows['qty'];
        } ?>
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
            <td style="border-top: solid; border-width: thin;" width="33%"></td>
            <td style="border-top: solid; border-width: thin;" width="33%"></td>
            <td style="border-top: solid; border-width: thin;" width="33%"></td>
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
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['no_faktur_mutasi'], 'reprint')?></span>
</div>
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
						<div class="col-sm-4"><b>Tanggal</b></div><div class="col-sm-8"><b> : </b><?=substr($row['tgl_mutasi'],0,10)?></div>
						<div class="col-sm-4"><b>No. Alokasi</b></div><div class="col-sm-8"><b> : </b><?=$row['no_faktur_mutasi']?></div>
						<div class="col-sm-4"><b>Lokasi Asal</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_lokasi_1']?></div>
						<div class="col-sm-4"><b>Lokasi Tujuan</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_lokasi_2']?></div>
					</div>
					<div class="col-sm-2"></div>
					<div class="col-sm-4">
						<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?=$this->m_website->get_nama_user($row['kd_kasir'])?></div>
						<div class="col-sm-4"><b>Status</b></div><div class="col-sm-8"><b> : </b><?php if($row['status']=='0'){echo "Packing";}else if($row['status']=='1'){echo "Packed";}else{echo "Received";}?></div>
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
									<th>Barcode/<?=substr($menu_group['as_deskripsi'],0,3)?></th>
									<th>Nama Barang</th>
									<th>Qty</th>
									<th>Harga</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no=0;
								foreach($detail as $rows){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=($rows['barcode']==$rows['kd_brg']?$rows['Deskripsi']:$rows['barcode'])?></td>
										<td><?=$rows['nm_brg']?></td>
										<td class="text-center"><?=(int)$rows['qty']?></td>
										<td class="text-right"><?=number_format($rows['hrg_jual'])?></td>
									</tr>
								<?php } ?>
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
				<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['no_faktur_mutasi'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php } ?>

<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); foreach($report as $row){ $i++; ?>
<div id="packing-<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><b>Tanggal</b></div><div class="col-sm-8"><b> : </b><?=substr($row['tgl_mutasi'],0,10)?></div>
						<div class="col-sm-4"><b>No. Alokasi</b></div><div class="col-sm-8"><b> : </b><?=$row['no_faktur_mutasi']?></div>
						<div class="col-sm-4"><b>Lokasi Asal</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_lokasi_1']?></div>
						<div class="col-sm-4"><b>Lokasi Tujuan</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_lokasi_2']?></div>
					</div>
					<div class="col-sm-2"></div>
					<div class="col-sm-4">
						<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?=$this->m_website->get_nama_user($row['kd_kasir'])?></div>
						<div class="col-sm-4"><b>Status</b></div><div class="col-sm-8"><b> : </b><?php if($row['status']=='0'){echo "Packing";}else if($row['status']=='1'){echo "Packed";}else{echo "Received";}?></div>
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<div class="panel-group panel-group-joined" id="accordion-test">
						<?php
						$read_data = $this->m_crud->read_data("master_packing","*","no_faktur_mutasi='".$row['no_faktur_mutasi']."'");

						foreach ($read_data as $row2) {
							?>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#accordion-test" href="#collapse<?=$row2['kd_packing']?>" class="collapsed">
												<?=$row2['kd_packing']?>
											</a>
										</h4>
									</div>
									<div id="collapse<?=$row2['kd_packing']?>" class="panel-collapse collapse">
										<div class="panel-body">
											<div class="row">
												<div class="col-sm-6">
													<div class="row">
														<label class="col-sm-4">Tgl Packing</label>
														<b class="col-sm-6">: <?=substr($row2['tgl_packing'],0,19)?></b>
													</div>
													<div class="row">
														<label class="col-sm-4">Pengirim</label>
														<b class="col-sm-6">: <?=$row2['pengirim']?></b>
													</div>
													<div class="row">
														<label class="col-sm-4">Operator</label>
														<b class="col-sm-6">: <?=$this->m_website->get_nama_user($row2['operator'])?></b>
													</div>
													<div class="row">
														<label class="col-sm-4">Status</label>
														<b class="col-sm-6">: <?php if($row2['status']=='0'){echo 'Sending';}else if($row2['status']=='0'){echo 'Received In Part';}else{echo 'Received';}?></b>
													</div>
												</div>
												<div class="col-sm-6"></div>
											</div>
											<table class="table table-bordered">
												<thead>
												<tr>
													<th>No</th>
													<th>Kode Barang</th>
													<th>Barcode/<?=substr($menu_group['as_deskripsi'],0,3)?></th>
													<th>Nama Barang</th>
													<th>Qty Alokasi</th>
													<th>Qty Packing</th>
													<th>Selisih</th>
													<th>Status</th>
												</tr>
												</thead>
												<tbody>
												<?php
												$no_pack = 1;
												$read_data_packing = $this->m_crud->read_data("master_packing mp, det_packing dp, Det_Mutasi dm, barang br","br.kd_brg, br.nm_brg, br.Deskripsi, br.barcode, dm.qty qty_alokasi, dp.qty qty_packing, mp.status","mp.kd_packing=dp.kd_packing AND dp.kd_brg=br.kd_brg AND dp.kd_brg=dm.kd_brg AND mp.no_faktur_mutasi=dm.no_faktur_mutasi AND mp.kd_packing='".$row2['kd_packing']."'");
												foreach ($read_data_packing as $row3) {
													?>
													<tr>
														<td><?=$no_pack?></td>
														<td><?=$row3['kd_brg']?></td>
														<td><?=($row3['barcode']==$row3['kd_brg']?$row3['Deskripsi']:$row3['barcode'])?></td>
														<td><?=$row3['nm_brg']?></td>
														<td><?=($row3['qty_alokasi']+0)?></td>
														<td><?=($row3['qty_packing']+0)?></td>
														<td><?=$row3['qty_alokasi']-$row3['qty_packing']?></td>
														<td><?=($row3['status']=='0')?"Sending":"Received"?></td>
													</tr>
													<?php
													$no_pack++;
												}
												?>
												</tbody>
											</table>
										</div>
									</div>
								</div>
						<?php
						}
						?>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
				<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['no_faktur_mutasi'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php } ?>

<script>
	function edit_trx(id, res) {
        if (res == true) {
            if (id.param == 'href_target_blank') {
                window.open("<?=base_url().'inventory/edit_alokasi/'?>" + id.kode);
            } else if(id.param == 'href') {
                window.location = "<?=base_url().'inventory/edit_alokasi/'?>" + id.kode;
            }
        }
    }

    function findPrice(Id, callback) {
        jQuery.ajax({
            url: "<?=base_url().'site/get_nama/'?>" + Id,
            cache: false,
            success: function(html) {
                callback(Id, html);
            }
        });
    }

    function receivePrice(Id, html) {
        alert("Product " + Id + " received HTML " + html);
    }

	function delete_trans(kode) {
		if (confirm('Akan menghapus data?')) {
		    $.ajax({
                url: "<?=base_url()?>inventory/cek_packing",
                type: "POST",
                data: {kode_mutasi:kode},
                success: function (res) {
                    if (res == true) {
                        hapus_otorisasi({param:'', kode:btoa(kode), activity:'Hapus Alokasi'}, delete_transaksi);
                    } else {
                        alert("Data tidak bisa dihapus! Data packing sudah diterima!")
                    }
                }
            });
		}
	}

	function delete_transaksi(id, res) {
	    if (res == true) {
            $.ajax({
                url: "<?php echo base_url() . 'inventory/delete_trx_alokasi' ?>",
                type: "POST",
                data: {kode_mutasi: atob(id.kode)},
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
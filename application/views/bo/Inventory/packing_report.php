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
											$data_option = $this->m_crud->read_data('Lokasi', 'Kode, Nama', null, 'Nama asc');
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
                                            $option['0P'] = 'Processing';
                                            $option['0S'] = 'Sending';
                                            $option['1'] = 'Received In Part';
                                            $option['2'] = 'Received';
                                            echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
                                            ?>
                                        </div>
                                    </div>
									<div class="col-sm-3">
										<div class="form-group">
											<label>Search</label>
											<?php $field = 'any'; ?>
											<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" placeholder="Packing/Operator/Pengirim/Mutasi/Lokasi" autofocus />
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
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Pilihan</th>
                                                <th>Tanggal</th>
                                                <th>Kode Packing</th>
                                                <th>Faktur Mutasi</th>
                                                <th>Faktur Pembelian</th>
                                                <th>Lokasi Asal</th>
                                                <th>Lokasi Tujuan</th>
                                                <th>Pengirim</th>
                                                <th>Operator</th>
                                                <th>Status</th>
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
                                                    <td class="text-center">
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
                                                            <ul class="dropdown-menu" role="menu">
                                                                <li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['kd_packing']?>', download_pdf)"><i class="md md-get-app"></i> Download</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['kd_packing']?>', cetak_pdf)" target="_blank"><i class="md md-print"></i> to PDF</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['kd_packing']?>', cetak, 'print_packing')"><i class="md md-print"></i> Print</a></li>
                                                                <li><a href="<?=base_url().'cetak/barcode_barang/'.base64_encode($row['kd_packing']).'/'.base64_encode('packing')?>" target="_blank"><i class="md md-print"></i> Barcode Barang</a></li>
                                                                <li><a href="<?=base_url().'cetak/barcode_packing/'.base64_encode($row['kd_packing'])?>" target="_blank"><i class="md md-print"></i> Barcode Packing</a></li>
                                                                <?php if ($row['status'] == 0) {
                                                                    echo '<li><a href = "#" onclick="edit_otorisasi({param:\'href_target_blank\', kode:\''.base64_encode($row['kd_packing']).'\', activity:\'Edit Packing\'}, edit_trx)" ><i class="md md-edit" ></i> Edit</a ></li>'; 
                                                                } ?>
                                                                <li><a href="#" id="delete" onclick="delete_trans('<?=$row['kd_packing']?>')"><i class="md md-close"></i> Delete</a></li>
                                                                <!--<li class="divider"></li>-->
                                                            </ul>
                                                        </div>
                                                    </td>
                                                    <td><?=substr($row['tgl_packing'],0,10)?></td>
                                                    <td><?=$row['kd_packing']?></td>
                                                    <td><?=$row['no_faktur_mutasi']?></td>
                                                    <td><?=$row['no_faktur_beli']?></td>
                                                    <td><?=$row['kd_lokasi_1']?></td>
                                                    <td><?=$row['kd_lokasi_2']?></td>
                                                    <td><?=$row['pengirim']?></td>
                                                    <td><?=$this->m_website->get_nama_user($row['operator'])?></td>
                                                    <td><?php if ($row['status'] == 0) {
															if($row['expedisi']=='0'){
																echo '<div class="panel panel-danger" style="margin-bottom: -1px"><div class="panel-heading text-center">Processing</div></div>';
															} else {
																echo '<div class="panel panel-warning" style="margin-bottom: -1px"><div class="panel-heading text-center">Sending</div></div>';
															}
                                                        } else if ($row['status'] == 1) {
                                                            echo '<div class="panel panel-primary" style="margin-bottom: -1px"><div class="panel-heading text-center">Received In Part</div></div>';
                                                        } else {
                                                            echo '<div class="panel panel-success" style="margin-bottom: -1px"><div class="panel-heading text-center" style="color: white">Received</div></div>';
                                                        } ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
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
						<div class="col-sm-4"><b>Tanggal</b></div><div class="col-sm-8"><b> : </b><?=substr($row['tgl_packing'],0,10)?></div>
						<div class="col-sm-4"><b>Kode Packing</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_packing']?></div>
						<div class="col-sm-4"><b>Faktur Mutasi</b></div><div class="col-sm-8"><b> : </b><?=$row['no_faktur_mutasi']?></div>
						<div class="col-sm-4"><b>Lokasi Asal</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_lokasi_1']?></div>
						<div class="col-sm-4"><b>Lokasi Tujuan</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_lokasi_2']?></div>
					</div>
					<div class="col-sm-2"></div>
					<div class="col-sm-4">
						<div class="col-sm-4"><b>Pengirim</b></div><div class="col-sm-8"><b> : </b><?=$row['pengirim']?></div>
						<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?=$this->m_website->get_nama_user($row['operator'])?></div>
                        <?=($row['status']!=0)?'<div class="col-sm-4"><b>Penerima</b></div><div class="col-sm-8"><b> : </b>'.$row['penerima'].'</div>':''; ?>
						<div class="col-sm-4"><b>Status</b></div><div class="col-sm-8"><b> : </b><?php if($row['status']=='0'){if($row['expedisi']=='0'){echo 'Processing';}else{echo 'Sending';}}else if($row['status']=='1'){echo 'Received In Part';}else {echo 'Received';} ?></div>
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
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
								<?php
                                $tqty = 0;
                                $no = 0;
								$detail = $this->m_crud->read_data("det_packing dp, barang br","dp.qty, br.kd_brg, br.barcode, br.nm_brg, dp.status, (SELECT hrg_jual FROM Det_Mutasi WHERE no_faktur_mutasi='".$row['no_faktur_mutasi']."' AND kd_brg=br.kd_brg GROUP BY hrg_jual) hrg_jual", "dp.kd_brg=br.kd_brg AND dp.kd_packing = '".$row['kd_packing']."'");
								foreach($detail as $rows){
								    $no++;
                                    ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=$rows['barcode']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td class="text-center"><?=(int)$rows['qty']?></td>
										<td class="text-right"><?=number_format($rows['hrg_jual'])?></td>
										<td><?=($rows['status']=='0')?($row['expedisi']=='0'?'Processing':'Sending'):'Received'?></td>
									</tr>
								<?php $tqty = $tqty + $rows['qty']; } ?>
							</tbody>
                            <tfoot>
                            <tr>
                                <th colspan="4">TOTAL</th>
                                <th class="text-center"><?=$tqty?></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
				<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['kd_packing'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="print_packing<?=$row['kd_packing']?>" class="hidden print-nota">
    <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['kd_packing']?>">
	<table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
		<thead>
		<tr>
			<td colspan="8" style="text-align: center">Packing Alokasi (<?=$row['kd_packing'].' / '.$row['no_faktur_mutasi']?>)</td>
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
			<td>Tanggal Packing</td>
			<td>:</td>
			<td><?=substr($row['tgl_packing'],0,10)?></td>

			<td></td>
			<td>Operator</td>
			<td>:</td>
			<td><?=$this->m_website->get_nama_user($row['operator'])?></td>
		</tr>
		<tr>
			<th></th>
			<td>Lokasi Dari-Ke</td>
			<td>:</td>
			<td><?=$row['kd_lokasi_1']?> - <?=$row['kd_lokasi_2']?></td>

			<td></td>
			<td>Pengirim</td>
			<td>:</td>
			<td><?=$row['pengirim']?></td>
		</tr>
		<tr>
			<th></th>
			<td></td>
			<td></td>
			<td></td>

			<td></td>
			<td>Status</td>
			<td>:</td>
			<td><?php if($row['status']=='0'){if($row['expedisi']=='0'){echo 'Processing';}else{echo 'Sending';}}else if($row['status']=='1'){echo 'Received In Part';}else {echo 'Received';} ?></td>
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
			<td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Status</td>
		</tr>
		</thead>
		<tbody>
		<?php $no = 0; $total = 0;
		foreach($detail as $rows){ $no++; ?>
			<tr>
				<td><?=$no?></td>
				<td><?=$rows['kd_brg']?></td>
				<td><?=$rows['barcode']?></td>
				<td><?=$rows['nm_brg']?></td>
				<td class="text-center"><?=(int)$rows['qty']?></td>
				<td class="text-right"><?=number_format($rows['hrg_jual'])?></td>
				<td><?=($rows['status']=='0')?($row['expedisi']=='0'?'Processing':'Sending'):'Received'?></td>
			</tr>
			<?php
		} ?>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="4" style="border-top: solid; border-width: thin">TOTAL</td>
			<td class="text-center" style="border-top: solid; border-width: thin"><?=$tqty?></td>
			<td style="border-top: solid; border-width: thin"></td>
			<td style="border-top: solid; border-width: thin"></td>
		</tr>
		</tfoot>
	</table>

	<table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
		<thead>
		<tr>
			<td style="border-top: solid; border-width: thin;" width="25%"></td>
			<td style="border-top: solid; border-width: thin;" width="25%"></td>
			<td style="border-top: solid; border-width: thin;" width="25%"></td>
			<td style="border-top: solid; border-width: thin;" width="25%"></td>
		</tr>
		</thead>
		<tbody>
		<tr>
            <td style="text-align:center;">
                <br/>Packing<br/><br/><br/>_____________
            </td>
			<td style="text-align:center;">
				<br/>Pengirim<br/><br/><br/>_____________
			</td>
			<td style="text-align:center;">
				<br/>Penerima<br/><br/><br/>_____________
			</td>
			<td style="text-align:center;">
				<br/>Mengetahui<br/><br/><br/>_____________
			</td>
		</tr>
		</tbody>
	</table>
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['kd_packing'], 'reprint')?></span>
</div>
<?php } ?>

<script>
    function pdf(id, res) {
        if (res == true) {
            if (id.param == 'print') {
                window.open("<?=base_url().strtolower($this->control).'/'.$page.'/'?>" + id.param + "/" + btoa(id.kode));
            } else {
                window.location = "<?=base_url().strtolower($this->control).'/'.$page.'/'?>" + id.param + "/" + btoa(id.kode);
            }
        }
    }
	
	function edit_trx(id, res) {
        if (res == true) {
			//add_activity('Edit Packing '+id.kode);
            if (id.param == 'href_target_blank') {
                window.open("<?=base_url().'inventory/edit_packing/'?>" + id.kode);
            } else if(id.param == 'href') {
                window.location = "<?=base_url().'inventory/edit_packing/'?>" + id.kode;
            }
        }
    }
	
	function delete_trans(kode) {
		if (confirm('Akan menghapus data?')) {
			var table_ = ['master_packing', 'det_packing', 'Kartu_stock'];
			var condition_ = ['kd_packing=\''+kode+'\'','kd_packing=\''+kode+'\'','kd_trx=\''+kode+'\''];
			
			hapus_otorisasi({param:'', kode:btoa(kode), activity:'Hapus Packing', table:table_, condition:condition_, kd_trx: kode}, delete_transaksi);
		}
	}
	
	function delete_transaksi(id, res) {
	    if (res == true) {
			$.ajax({
				url: "<?php echo base_url() . 'inventory/remove_trans_packing' ?>",
				type: "POST",
				data: {table: id.table, condition: id.condition, kd_trx_:id.kd_trx},
				success: function (res) {
					if (res == true) {
						//add_activity('Delete Packing '+kode);
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

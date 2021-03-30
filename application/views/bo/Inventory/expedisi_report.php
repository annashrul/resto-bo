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
                                            $option['0'] = 'Sending';
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
											<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" placeholder="Expedisi/Pengirim/Operator" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
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
												<th>Kode Expedisi</th>
												<th>Lokasi Asal</th>
												<th>Lokasi Tujuan</th>
												<th>Pengirim</th>
												<th>Operator</th>
												<th>Status</th>
												<th>Pilihan</th>
											</tr>
										</thead>
										<tbody>
											<?php $no = 0 + (($this->uri->segment(3)!=null)?(($this->uri->segment(3)-1) * 30):0);
											foreach($report as $row) {
												$no++;
												?>
												<tr>
													<td><?=$no?></td>
													<td><?=substr($row['tgl_expedisi'],0,10)?></td>
													<td><?=$row['kd_expedisi']?></td>
													<td><?=$row['nama_lokasi_asal']?></td>
													<td><?=$row['nama_lokasi_tujuan']?></td>
													<td><?=$row['pengirim']?></td>
													<td><?=$row['nama_operator']?></td>
													<td>
														<?php if ($row['status'] == '0') {
                                                            echo '<div class="panel panel-warning" style="margin-bottom: -1px"><div class="panel-heading text-center">Sending</div></div>';
                                                        } else if ($row['status'] == '1') {
                                                            echo '<div class="panel panel-primary" style="margin-bottom: -1px"><div class="panel-heading text-center">Received In Part</div></div>';
                                                        } else if ($row['status'] == '2') {
                                                            echo '<div class="panel panel-success" style="margin-bottom: -1px"><div class="panel-heading text-center" style="color: white">Received</div></div>';
                                                        } ?>
                                                    </td>
													<td class="text-center">
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
															<ul class="dropdown-menu" role="menu">
																<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['kd_expedisi']?>', download_pdf)"><i class="md md-get-app"></i> Download</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['kd_expedisi']?>', cetak_pdf)" target="_blank"><i class="md md-print"></i> to PDF</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['kd_expedisi']?>', cetak, 'print_alokasi')"><i class="md md-print"></i> Print</a></li>
                                                                <li><a href="#" id="delete" onclick="delete_trans('<?=$row['kd_expedisi']?>')"><i class="md md-close"></i> Delete</a></li>
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
<div id="print_alokasi<?=$row['kd_expedisi']?>" class="hidden print-nota">
    <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['kd_expedisi']?>">
    <table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
        <thead>
        <tr>
            <td colspan="8" style="text-align: center;">Expedisi Barang</td>
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
            <td><?=substr($row['tgl_expedisi'],0,10)?></td>

            <td></td>
            <td>Operator</td>
            <td>:</td>
            <td><?=$row['nama_operator']?></td>
        </tr>
        <tr>
            <th></th>
            <td>Kode Expedisi</td>
            <td>:</td>
            <td><?=$row['kd_expedisi']?></td>

            <td></td>
            <td>Pengirim</td>
            <td>:</td>
            <td><?=$row['pengirim']?></td>
        </tr>
        <tr>
            <th></th>
            <td>Lokasi Asal</td>
            <td>:</td>
            <td><?=$row['nama_lokasi_asal']?></td>

            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <th></th>
            <td>Lokasi Tujuan</td>
            <td>:</td>
            <td><?=$row['nama_lokasi_tujuan']?></td>

            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        </tbody>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New'; font-size:9pt;">
        <thead>
        <tr>
            <td style="width: 5%; border-bottom: solid; border-width: thin; padding-left: 5pt" rowspan="2">No</td>
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt" rowspan="2">Kode Packing</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt" rowspan="2">Kode Mutasi</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt" rowspan="2">Nama Supp/Jns Brg</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt" rowspan="2">Jumlah Koli</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt" colspan="3">Menerima</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt" rowspan="2">Keterangan</td>
        </tr>
        <tr>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt">BRG</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt">SJ</td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt">TTD</td>
        </tr>
        </thead>
        <tbody>
        <?php $no = 0;
        $detail = $this->m_crud->join_data('det_expedisi de', 'de.*, mp.no_faktur_mutasi', array('master_packing mp'), array('mp.kd_packing=de.kd_packing'), "de.kd_expedisi = '".$row['kd_expedisi']."'");
        foreach($detail as $rows){ $no++; ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$rows['kd_packing']?></td>
                <td><?=$rows['no_faktur_mutasi']?></td>
                <td><?=$rows['ket']?></td>
                <td><?=$rows['jml_koli']?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php
            } ?>
        </tbody>
    </table>

    <table width="100%" style="letter-spacing: 5px; font-family: 'Courier New';font-size: 9pt">
        <thead>
        <tr>
            <td style="border-top: solid; border-width: thin;" width="20%"></td>
            <td style="border-top: solid; border-width: thin;" width="20%"></td>
            <td style="border-top: solid; border-width: thin;" width="20%"></td>
            <td style="border-top: solid; border-width: thin;" width="20%"></td>
            <td style="border-top: solid; border-width: thin;" width="20%"></td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td style="text-align:center;">
                <br/>Operator<br/><br/><br/>_____________
            </td>
            <td style="text-align:center;">
                <br/>Supir<br/><br/><br/>_____________
            </td>
            <td style="text-align:center;">
                <br/>Checker<br/><br/><br/>_____________
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
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['kd_expedisi'], 'reprint')?></span>
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
						<div class="col-sm-5"><b>Tanggal</b></div><div class="col-sm-7"><b> : </b><?=substr($row['tgl_expedisi'],0,10)?></div>
						<div class="col-sm-5"><b>No. Expedisi</b></div><div class="col-sm-7"><b> : </b><?=$row['kd_expedisi']?></div>
						<div class="col-sm-5"><b>Lokasi Asal</b></div><div class="col-sm-7"><b> : </b><?=$row['nama_lokasi_asal']?></div>
						<div class="col-sm-5"><b>Lokasi Tujuan</b></div><div class="col-sm-7"><b> : </b><?=$row['nama_lokasi_tujuan']?></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><b>Operator</b></div><div class="col-sm-7"><b> : </b><?=$row['nama_operator']?></div>
						<div class="col-sm-5"><b>Pengirim</b></div><div class="col-sm-7"><b> : </b><?=$row['pengirim']?></div>
					</div>
				</div>	
				<hr/>
				<div class="row">
					<div class="col-md-12 col-sm-12 col-xs-12">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
									<th rowspan="2">No</th>
									<th rowspan="2">Kode Packing</th>
									<th rowspan="2">Kode Mutasi</th>
									<th rowspan="2">Nama Supp/Jns Brg</th>
									<th rowspan="2">Jumlah Koli</th>
									<th colspan="3">Menerima</th>
									<th rowspan="2">Keterangan</th>
									<th rowspan="2">Status</th>
								</tr>
                                <tr>
                                    <th>BRG</th>
                                    <th>SJ</th>
                                    <th>TTD</th>
                                </tr>
							</thead>
							<tbody>
								<?php
								$no=0;
								foreach($detail as $rows){ $no++; ?>
									<tr>
                                        <td><?=$no?></td>
                                        <td><?=$rows['kd_packing']?></td>
                                        <td><?=$rows['no_faktur_mutasi']?></td>
                                        <td><?=$rows['ket']?></td>
                                        <td><?=$rows['jml_koli']?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
											<?php if ($rows['status'] == '0') {
												echo '<div class="panel panel-warning" style="margin-bottom: -1px"><div class="panel-heading text-center">Sending</div></div>';
											} else if ($rows['status'] == '1') {
												echo '<div class="panel panel-success" style="margin-bottom: -1px"><div class="panel-heading text-center" style="color: white">Received</div></div>';
											} ?>
										</td>
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
				<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['no_receive_order'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php } ?>

<script>
	function delete_trans(kode) {
		if (confirm('Akan menghapus data?')) {
			var table_ = ['master_expedisi', 'det_expedisi', 'kartu_stock'];
			var condition_ = ['kd_expedisi=\''+kode+'\'','kd_expedisi=\''+kode+'\'', 'keterangan like \'%('+kode+')\''];
			
			hapus_otorisasi({param:'', kode:btoa(kode), activity:'Hapus Expedisi', table:table_, condition:condition_, kd_trx: kode}, delete_transaksi);
		}
	}
	
	function delete_transaksi(id, res) {
	    if (res == true) {
			$.ajax({
				url: "<?php echo base_url() . 'site/delete_ajax_trx' ?>",
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
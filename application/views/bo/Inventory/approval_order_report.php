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
											<label>Lokasi Asal</label>
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
												<th>No. Approve</th>
												<th>No. Order</th>
												<th>No. Alokasi</th>
												<th>Lokasi Asal</th>
												<th>Operator</th>
												<th>Pilihan</th>
											</tr>
										</thead>
										<tbody>
											<?php $no = 0 + (($this->uri->segment(3)!=null)?(($this->uri->segment(3)-1) * 30):0);
											foreach($report as $row) {
												$no++;
												$this->m_export_file->create_barcode(array("code"=>$row['no_faktur_mutasi'], "file"=>$row['no_faktur_mutasi']));
												?>
												<tr>
													<td><?=$no?></td>
													<td><?=substr($row['tgl_receive_order'],0,10)?></td>
													<td><?=$row['no_receive_order']?></td>
													<td><?=$row['no_order']?></td>
													<td><?=$row['no_faktur_mutasi']?></td>
													<td><?=$row['nama_lokasi']?></td>
													<td><?=$row['nama_operator']?></td>
													<td class="text-center">
														<div class="btn-group">
															<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
															<ul class="dropdown-menu" role="menu">
																<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['no_receive_order']?>', download_pdf)"><i class="md md-get-app"></i> Download</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['no_receive_order']?>', cetak_pdf)" target="_blank"><i class="md md-print"></i> to PDF</a></li>
                                                                <li><a href="#" onclick="re_print('<?=$row['no_receive_order']?>', cetak, 'print_alokasi')"><i class="md md-print"></i> Print</a></li>
                                                                <?php
                                                                if ($row['status_mutasi'] == 0) { ?>
																<li><a href="#" id="delete" onclick="delete_trx('<?=$row['no_receive_order']?>', delete_trans)"><i class="md md-close"></i> Delete</a></li>
                                                                <?php } ?>
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
<div id="print_alokasi<?=$row['no_receive_order']?>" class="hidden print-nota">
    <img style="float: right; margin-top: -10px" src="<?=base_url().'barcode.php?size=30&sizefactor=2&text='.$row['no_receive_order']?>">
    <table width="100%" cellspacing="0" cellpadding="1" style="letter-spacing: 5px; font-family: 'Courier New'; margin-bottom: 10px; font-size: 9pt">
        <thead>
        <tr>
            <td colspan="8" style="text-align: center;">Approve Order (<?=$row['no_receive_order']?>)</td>
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
            <td><?=substr($row['tgl_receive_order'],0,10)?></td>

            <td></td>
            <td>Operator</td>
            <td>:</td>
            <td><?=$row['nama_operator']?></td>
        </tr>
        <tr>
            <th></th>
            <td>No. Order</td>
            <td>:</td>
            <td><?=$row['no_order']?></td>

            <td></td>
            <td>Lokasi</td>
            <td>:</td>
            <td><?php $row['nama_lokasi'] ?></td>
        </tr>
        <tr>
            <th></th>
            <td>No. Alokasi</td>
            <td>:</td>
            <td><?=$row['no_faktur_mutasi']?></td>

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
            <td style="width: 5%; border-bottom: solid; border-width: thin; padding-left: 5pt">No</td>
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Kode Barang</td>
            <td style="width: 15%; border-bottom: solid; border-width: thin; padding-left: 5pt">Barcode/<?=substr($menu_group['as_deskripsi'],0,3)?></td>
            <td style="width: 40%; border-bottom: solid; border-width: thin; padding-left: 5pt">Nama Barang</td>
            <td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-center">Qty Order</td>
            <td style="width: 10%; border-bottom: solid; border-width: thin; padding-left: 5pt" class="text-center">Qty Approve</td>
        </tr>
        </thead>
        <tbody>
        <?php $no = 0;
        $qty_order = 0;
        $qty_approve = 0;
        $detail = $this->m_crud->join_data('det_order do', 'br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, ISNULL(dro.qty, 0) qty_approve, do.qty qty_order', array('barang br', array('table'=>'master_receive_order mro','type'=>'LEFT'), array('table'=>'det_receive_order dro','type'=>'LEFT')), array('br.kd_brg=do.kd_brg','mro.no_order=do.no_order','dro.no_receive_order=mro.no_receive_order and dro.kd_brg=do.kd_brg'), "mro.no_receive_order = '".$row['no_receive_order']."'");
        foreach($detail as $rows){ $no++; ?>
            <tr>
                <td><?=$no?></td>
                <td><?=$rows['kd_brg']?></td>
                <td><?=$rows['kd_brg']==$rows['barcode']?$rows['Deskripsi']:$rows['barcode']?></td>
                <td><?=$rows['nm_brg']?></td>
                <td class="text-center"><?=(int)$rows['qty_order']?></td>
                <td class="text-center"><?=(int)$rows['qty_approve']?></td>
            </tr>
            <?php
            $qty_order = $qty_order + (int)$rows['qty_order'];
            $qty_approve = $qty_approve + (int)$rows['qty_approve'];
        } ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4" style="border-top: solid; border-width: thin">TOTAL</td>
            <td class="text-center" style="border-top: solid; border-width: thin"><?=$qty_order?></td>
            <td class="text-center" style="border-top: solid; border-width: thin"><?=$qty_approve?></td>
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

            </td>
            <td style="text-align:center;">

            </td>
            <td style="text-align:center;">
                <br/>Operator<br/><br/><br/>_____________
            </td>
        </tr>
        </tbody>
    </table>
    <span style="font-weight: bold; font-style: italic"><?=$this->m_website->reprint($row['no_receive_order'], 'reprint')?></span>
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
						<div class="col-sm-5"><b>Tanggal</b></div><div class="col-sm-7"><b> : </b><?=substr($row['tgl_receive_order'],0,10)?></div>
						<div class="col-sm-5"><b>No. Approve Order</b></div><div class="col-sm-7"><b> : </b><?=$row['no_receive_order']?></div>
						<div class="col-sm-5"><b>No. Order</b></div><div class="col-sm-7"><b> : </b><?=$row['no_order']?></div>
						<div class="col-sm-5"><b>No. Alokasi</b></div><div class="col-sm-7"><b> : </b><?=$row['no_faktur_mutasi']?></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-5"><b>Lokasi Asal</b></div><div class="col-sm-7"><b> : </b><?=$row['nama_lokasi']?></div>
						<div class="col-sm-5"><b>Operator</b></div><div class="col-sm-7"><b> : </b><?=$row['nama_operator']?></div>
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
									<th>Barcode/<?=substr($menu_group['as_deskripsi'],0,3)?></th>
									<th>Nama Barang</th>
									<th>Qty Order</th>
									<th>Qty Approve</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no=0;
								foreach($detail as $rows){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=$rows['kd_brg']==$rows['barcode']?$rows['Deskripsi']:$rows['barcode']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td class="text-center"><?=(int)$rows['qty_order']?></td>
										<td class="text-center"><?=(int)$rows['qty_approve']?></td>
									</tr>
								<?php
								} ?>
							</tbody>
                            <tfoot>
                            <tr>
                                <th class="text-left" colspan="4">TOTAL</th>
                                <th class="text-center"><?=$qty_order?></th>
                                <th class="text-center"><?=$qty_approve?></th>
                            </tr>
                            </tfoot>
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
	function delete_trans(kode, res) {
	    if (res == true) {
            if (confirm('Akan menghapus data?')) {
                $.ajax({
                    url: "<?php echo base_url() . 'inventory/delete_receive_order/' ?>" + btoa(kode),
                    type: "GET",
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
	}

	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script>
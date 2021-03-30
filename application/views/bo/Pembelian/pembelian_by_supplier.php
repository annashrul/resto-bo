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
											<th style="width: 10px">No</th>
											<th>Kode Supplier</th>
											<th>Nama Supplier</th>
											<th>Total Pembelian</th>
											<th>Pilihan</th>
										</tr>
										</thead>
										<tbody>
										<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0);
										$tp = 0;
										foreach($report as $row){
										    $no++;
										    ?>
											<tr>
												<td><?=$no?></td>
												<td><?=$row['kode']?></td>
												<td><?=$row['Nama']?></td>
												<td class="text-right"><?=number_format($row['total_pembelian'], 2)?></td>
												<td class="text-center">
													<div class="btn-group">
														<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
														<ul class="dropdown-menu" role="menu">
															<li><a href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a></li>
															<li><a href="<?=base_url().strtolower($this->control).'/'.$page.'/download/'.base64_encode($row['kode'])?>"><i class="md md-get-app"></i> Download</a></li>
															<li><a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['kode'])?>" target="_blank"><i class="md md-print"></i> to PDF</a></li>
															<!--<li><a href="<?/*=base_url().'cetak/nota_pembelian/'.base64_encode($row['kd_brg'])*/?>" target="_blank"><i class="md md-print"></i> Nota</a></li>-->

															<!--<li class="divider"></li>-->
														</ul>
													</div>
												</td>
											</tr>
										<?php
										$tp = $tp + (float)$row['total_pembelian'];
										} ?>
										</tbody>
										<tfoot>
										<tr>
											<th colspan="3">TOTAL PER PAGE</th>
											<th class="text-right"><?=number_format($tp, 2)?></th>
											<th></th>
										</tr>
										<tr>
											<th colspan="3">TOTAL</th>
											<th class="text-right"><?=number_format($total['total_pembelian'], 2)?></th>
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

<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 15):0); foreach($report as $row){ $i++; ?>
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
							<div class="col-sm-4"><b>Kode Supplier</b></div><div class="col-sm-8"><b> : </b><?=$row['kode']?></div>
							<div class="col-sm-4"><b>Nama Supplier</b></div><div class="col-sm-8"><b> : </b><?=$row['Nama']?></div>
						</div>
						<!--<div class="col-sm-2"></div>
						<div class="col-sm-4">
							<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?/*=$row['Operator']*/?></div>
							<div class="col-sm-4"><b>Pelunasan</b></div><div class="col-sm-8"><b> : </b><?/*=$row['Pelunasan']*/?></div>
						</div>-->
					</div>
					<hr/>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<table id="datatable" class="table table-striped table-bordered">
								<thead>
								<tr>
									<th>No</th>
									<th>Tanggal</th>
									<th>No. Transaksi</th>
									<th>Nota Supplier</th>
									<th>Nilai Pembelian</th>
									<th>Jenis Transaksi</th>
									<!--<th>Uang Muka</th>
									<th>Pembayaran</th>-->
									<th>Jatuh Tempo</th>
									<!--<th>Status</th>-->
								</tr>
								</thead>
								<tbody>
                                <?php
                                $no = 0; $tdp = 0;
                                //And det_beli.tgl_Beli between '".$tgl_awal."' and '".$tgl_ahir."' and master_beli.lokasi='".$lokasi."';
                                $condition = "kode_supplier='".$row['kode']."' AND (tgl_beli >= '".$tgl_awal." 00:00:00' and tgl_beli <= '".$tgl_akhir." 23:59:59')";
                                ($lokasi==null)?null:$condition .= " and lokasi='".$lokasi."'";
                                $column = "tgl_beli, no_faktur_beli, noNota, type, tgl_jatuh_tempo, (total_beli-disc+ppn) total_beli";
                                $detail = $this->m_crud->read_data("pembelian_report", $column, $condition, "no_faktur_beli ASC", "tgl_beli, no_faktur_beli, noNota, type, tgl_jatuh_tempo, total_beli, disc, ppn");
                                foreach($detail as $rows){
                                    $no++;
                                    $sub_total = 0;
                                    ?>
                                    <tr>
                                        <td><?=$no?></td>
                                        <td><?=substr($rows['tgl_beli'], 0, 10)?></td>
                                        <td><?=$rows['no_faktur_beli']?></td>
                                        <td><?=$rows['noNota']?></td>
                                        <td style="text-align:right;"><?=number_format($rows['total_beli'], 2)?></td>
                                        <td><?=$rows['type']?></td>
                                        <td><?=substr($rows['tgl_jatuh_tempo'], 0, 10)?></td>
                                    </tr>
                                    <?php
                                    $tdp = $tdp + $rows['total_beli'];
                                } ?>
								</tbody>
								<tfoot>
								<tr>
									<th colspan="4">TOTAL</th>
									<th class="text-right"><?=number_format($tdp, 2)?></th>
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
					<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['kode'])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } ?>

<script>
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script>
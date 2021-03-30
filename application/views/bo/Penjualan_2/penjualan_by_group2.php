<style>
	table {
		font-size: 9pt !important;
	}
	th {
		font-size: 10pt !important;
		vertical-align: middle !important;
		text-align: center;
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
								<div class="col-sm-3">
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
											<th>Kode <?=$menu_group['as_group2']?></th>
											<th>Nama <?=$menu_group['as_group2']?></th>
											<th>Qty Terjual</th>
											<th>Gross Sales</th>
											<th>Diskon Item</th>
											<th>Net Sales</th>
											<th style="width: 100px">Pilihan</th>
										</tr>
										</thead>
										<tbody>
										<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); $qt = 0; $di = 0; $gs = 0; $ns = 0; foreach($report as $row){ $no++; ?>
											<tr>
												<td><?=$no?></td>
												<td><?=$row['Kode']?></td>
												<td><?=$row['Nama']?></td>
												<td><?=($row['qty']+0).' '.$row['satuan']?></td>
												<td class="text-right"><?=number_format($row['gross_sales'],2)?></td>
												<td class="text-right"><?=number_format($row['diskon'],2)?></td>
												<td class="text-right"><?=number_format($row['gross_sales']-$row['diskon'],2)?></td>
												<td class="text-center">
													<a href="#" class="btn btn-icon waves-effect waves-light btn-primary btn-xs m-b-5" data-toggle="modal" title="Detail" data-target="#<?=$no?>"><i class="md md-visibility"></i></a>
													<a href="<?=base_url().strtolower($this->control).'/'.$page.'/download/'.base64_encode($row['Kode'])?>" class="btn btn-icon waves-effect waves-light btn-primary btn-xs m-b-5" title="Download"><i class="md md-get-app"></i></a>
													<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['Kode'])?>" class="btn btn-icon waves-effect waves-light btn-primary btn-xs m-b-5" target="_blank" title="to PDF"><i class="md md-print"></i></a>
												</td>
											</tr>
										<?php
										$qt = $qt + (int)$row['qty'];
										$gs = $gs + (float)$row['gross_sales'];
										$di = $di + (float)$row['diskon'];
										$ns = $ns + (float)$row['gross_sales']-(float)$row['diskon'];
										} ?>
										</tbody>
										<tfoot>
										<tr>
											<th colspan="3">TOTAL PER PAGE</th>
											<th class="text-right"><?=($qt+0)?></th>
											<th class="text-right"><?=number_format($gs, 2)?></th>
											<th class="text-right"><?=number_format($di, 2)?></th>
											<th class="text-right"><?=number_format($ns, 2)?></th>
											<th></th>
										</tr>
										<tr>
											<th colspan="3">TOTAL</th>
											<th class="text-right"><?=($tqt+0)?></th>
											<th class="text-right"><?=number_format($tgs, 2)?></th>
											<th class="text-right"><?=number_format($tdi, 2)?></th>
											<th class="text-right"><?=number_format($tns, 2)?></th>
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

<?php $i = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); foreach($report as $row){ $i++; ?>
	<div id="<?=$i?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
		<div class="modal-dialog modal-full">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-8">
							<div class="col-sm-4"><b>Kode <?=$menu_group['as_group2']?></b></div><div class="col-sm-8"><b> : </b><b id="kd_brg<?=$i?>"><?=$row['Kode']?></b></div>
							<div class="col-sm-4"><b>Nama <?=$menu_group['as_group2']?></b></div><div class="col-sm-8"><b> : </b><b><?=$row['Nama']?></b></div>
						</div>
						<div class="col-sm-4">
							<div class="row">
								<b><?=$periode?><b>
							</div>
							<div class="row">
								<b><?=$q_lokasi?><b>
							</div>
						</div>
					</div>
					<hr/>
					<div class="row">
						<div class="col-md-12 col-sm-12 col-xs-12">
							<table class="table table-striped table-bordered">
								<thead>
								<tr>
									<th>No</th>
									<th>No. Faktur</th>
									<th>Tanggal</th>
									<th>Kode Barang</th>
									<th>Barcode</th>
									<th>Nama Barang</th>
									<th><?=$menu_group['as_deskripsi']?></th>
									<th>Qty</th>
									<th>Gross Sales</th>
									<th>Diskon Item</th>
									<th>Net Sales</th>
								</tr>
								</thead>
								<tbody id="det<?=$i?>">
								<?php
								$no = 0;

								$total_qty = 0;
								$total_gross_sales = 0;
								$total_diskon_item = 0;
								$total_net_sales = 0;

								$where = "mt.HR='S' AND mt.kd_trx=dt.kd_trx AND dt.qty>0 AND dt.kd_brg=br.kd_brg AND br.Group2='".$row['Kode']."'";
								$where .= " AND ".$tgl;
								$where .= ($lokasi==null)?"":$where.=" AND mt.Lokasi='".$lokasi."'";

								$detail = $this->m_crud->read_data("Master_Trx mt, Det_Trx dt, barang br","mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi, SUM(dt.qty) qty, SUM(dt.qty*dt.hrg_jual) gross_sales, SUM(dt.dis_persen) diskon",$where,"mt.kd_trx DESC","mt.kd_trx, mt.tgl, br.kd_brg, br.barcode, br.nm_brg, br.Deskripsi");

								foreach($detail as $rows){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_trx']?></td>
										<td><?=substr($rows['tgl'], 1, 10)?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=$rows['barcode']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td><?=$rows['Deskripsi']?></td>
										<td class="text-center"><?=($rows['qty']+0)?></td>
										<td class="text-right"><?=number_format($rows['gross_sales'],2)?></td>
										<td class="text-right"><?=number_format($rows['diskon'],2)?></td>
										<td class="text-right"><?=number_format($rows['gross_sales']-$rows['diskon'],2)?></td>
									</tr>
								<?php
								$total_qty = $total_qty + ($rows['qty']+0);
								$total_gross_sales = $total_gross_sales + $rows['gross_sales'];
								$total_diskon_item = $total_diskon_item + $rows['diskon'];
								$total_net_sales = $total_net_sales + ($rows['gross_sales']-$rows['diskon']);
								} ?>
								</tbody>
								<tfoot>
								<tr>
									<th colspan="7">TOTAL</th>
									<th class="text-center"><?=$total_qty?></th>
									<th class="text-right"><?=number_format($total_gross_sales, 2)?></th>
									<th class="text-right"><?=number_format($total_diskon_item, 2)?></th>
									<th class="text-right"><?=number_format($total_net_sales, 2)?></th>
								</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
					<!--<button class="btn btn-primary waves-effect waves-light" onclick="to_pdf(<?/*=$i*/?>)"><i class="md md-print"></i> to PDF</button>-->
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
<?php } ?>

<script>
	function to_pdf(id) {
		var filter = $("#filter"+id).val();
		var kode = document.getElementById('kd_supp'+id).innerHTML;

		window.open("<?=base_url().'penjualan/penjualan_by_supplier/print/'?>" + btoa(kode));
	}
	
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script>


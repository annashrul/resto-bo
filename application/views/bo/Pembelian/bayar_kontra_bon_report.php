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
									<div id="daterange_all" style="cursor: pointer;">
										<input type="text" name="<?=$field?>" id="<?=$field?>" class="form-control" style="height: 40px;" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y/m/d")." - ".date("Y/m/d"))?>">
									</div>
								</div>
								<div class="col-sm-2">
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
										<label>Supplier</label>
										<?php $field = 'supplier';
										$option = null; $option[''] = 'Semua Supplier';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('supplier', 'kode, Nama', null, 'Nama asc');
										foreach($data_option as $row){ $option[$row['kode']] = $row['kode'].' | '.$row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										?>
									</div>
								</div>
								<div class="col-sm-2">
									<div class="form-group">
										<label>Search</label>
										<?php $field = 'any'; ?>
										<input class="form-control" type="text" id="<?=$field?>" name="<?=$field?>" placeholder="Remark 1/Remark 2" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
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
											<!--<th>Pilihan</th>-->
											<th>Name</th>
											<th>Trans Date</th>
											<th>Trans Amount</th>
											<th>Retur</th>
											<th>Acc. No.</th>
											<th>Bank Name</th>
											<th>BI Code</th>
											<th>Bank Branch Name</th>
											<th>Remark 1</th>
											<th>Remark 2</th>
											<th>Jenis</th>
											<th>Rec</th>
											<th>Receiv</th>
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
												<!--<td class="text-center">
													<div class="btn-group">
														<button type="button" class="btn btn-primary dropdown-toggle waves-effect waves-light" data-toggle="dropdown" aria-expanded="false">Pilihan <span class="caret"></span></button>
														<ul class="dropdown-menu" role="menu">
															<li><a href="#" onclick="modal_detail('<?=$row['id_master_kontra']?>');"><i class="md md-visibility"></i> Detail</a></li>
															<li><a href="<?=base_url().strtolower($this->control).'/'.$page.'/download/'.base64_encode($row['id_master_kontra'])?>"><i class="md md-get-app"></i> Download</a></li>
															<li><a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row['id_master_kontra'])?>" target="_blank"><i class="md md-print"></i> to PDF</a></li>
														</ul>
													</div>
												</td>-->
												<td><?=$row['an']?></td>
												<td><?=substr($row['tgl'],0,10)?></td>
												<td class="text-right"><?=number_format($row['bayar_kontra'], 2)?></td>
												<td class="text-right"><?=number_format($row['retur_kontrabon'], 2)?></td>
												<td><?=$row['acc_no']?></td>
												<td><?=$row['bank']?></td>
												<td><?=$row['bi_code']?></td>
												<td><?=$row['bank_branch']?></td>
												<td><?=$row['master_kontra']?></td>
												<td><?=$row['nama_supplier']?></td>
												<td><?=$row['jenis']?></td>
												<td><?=$row['rec']?></td>
												<td><?=$row['receiv']?></td>
											</tr>
										<?php
										$tp = $tp + (float)$row['bayar_kontra'];
										} ?>
										</tbody>
										<tfoot>
										<tr>
											<th colspan="3">TOTAL PER PAGE</th>
											<th class="text-right"><?=number_format($tp, 2)?></th>
											<th colspan="10"></th>
										</tr>
										<tr>
											<th colspan="3">TOTAL</th>
											<th class="text-right"><?=number_format($detail['total_bayar_kontrabon'], 2)?></th>
											<th colspan="10"></th>
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

<div id="modal-detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-full">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6">
						<div class="col-sm-4"><b>No. Kontra Bon</b></div><div class="col-sm-8"><b> : </b><b id="kontrabon"></b></div>
						<div class="col-sm-4"><b>Supplier</b></div><div class="col-sm-8"><b> : </b><b id="supplier"></b></div>
					</div>
					<div class="col-sm-6">
						<div class="col-sm-4"><b>Tanggal Kontra Bon</b></div><div class="col-sm-8"><b> : </b><b id="tgl-kontra"></b></div>
						<div class="col-sm-4"><b>Tanggal Jatuh Tempo</b></div><div class="col-sm-8"><b> : </b><b id="tgl-bayar"></b></div>
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
								<th>Nota Pembelian</th>
								<th>Nota Supplier</th>
								<th>Nilai Pembelian</th>
								<th>Nilai Kontra Bon</th>
							</tr>
							</thead>
							<tbody id="list_detail">
							</tbody>
							<tfoot>
							<tr>
								<th colspan="4"><b>TOTAL</b></th>
								<th class="text-right"><b id="total"></b></th>
							</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
				<!--
				<a href="<?=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode($row[''])?>" target="_blank"><button type="button" class="btn btn-primary waves-effect waves-light"><i class="md md-print"></i> to PDF</button></a>
				-->
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
	
	function modal_detail(nota=''){
		$.ajax({
            url: "<?= base_url() . $this->control . '/read_detail_kontra/' ?>"+btoa(nota),
            type: "GET",
            dataType: "JSON",
            success: function (data) {
				$('#kontrabon').text($('#master_kontra'+nota).text());
				$('#supplier').text($('#nama_supplier'+nota).text());
				$('#tgl-kontra').text($('#tgl_kontra'+nota).text());
				$('#tgl-bayar').text($('#tgl_bayar'+nota).text());
				$('#modal-detail').modal('show'); 
				$("#list_detail").html(data.list_detail);
                $("#total").text(to_rp(data.total));
            }
        });
	}
</script>

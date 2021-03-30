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
                                            <label>Status</label>
                                            <?php $field = 'status';
                                            $option = null;
                                            $option[''] = 'Semua Status';
                                            $option['0'] = 'Belum Diproses';
                                            $option['1'] = 'Selesai';
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
												<th>No. Order</th>
												<th>Lokasi Asal</th>
												<th>Operator</th>
												<th>Status</th>
												<th>Pilihan</th>
											</tr>
										</thead>
										<tbody>
											<?php $no = 0 + (($this->uri->segment(3)!=null)?(($this->uri->segment(3)-1) * 30):0);
											foreach($report as $row){
												$no++;
												?>
												<tr>
													<td><?=$no?></td>
													<td><?=substr($row['tgl_order'],0,10)?></td>
													<td><?=$row['no_order']?></td>
													<td><?=$row['nama_lokasi']?></td>
													<td><?=$row['nama_kasir']?></td>
													<td><?php if ($row['status'] == 0){
															echo '<div class="panel panel-warning" style="margin-bottom: -1px"><div class="panel-heading text-center">Belum Diproses</div></div>';
														} else {
															echo '<div class="panel panel-success" style="margin-bottom: -1px"><div class="panel-heading text-center" style="color: white">Selesai</div></div>';
														}?>
													</td>
													<td class="text-center">
                                                        <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#<?=$no?>"><i class="md md-visibility"></i> Detail</a>
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
						<div class="col-sm-4"><b>Tanggal</b></div><div class="col-sm-8"><b> : </b><?=substr($row['tgl_order'],0,10)?></div>
						<div class="col-sm-4"><b>No. Order</b></div><div class="col-sm-8"><b> : </b><?=$row['no_order']?></div>
						<div class="col-sm-4"><b>Lokasi Asal</b></div><div class="col-sm-8"><b> : </b><?=$row['nama_lokasi']?></div>
					</div>
					<div class="col-sm-2"></div>
					<div class="col-sm-4">
						<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?=$row['nama_kasir']?></div>
						<div class="col-sm-4"><b>Status</b></div><div class="col-sm-8"><b> : </b><?php if($row['status']=='0'){echo "Belum Diproses";}else{echo "Selesai";}?></div>
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
									<th>Qty</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no=0;
								$total = 0;
                                $detail = $this->m_crud->join_data('det_order do', 'br.kd_brg, br.barcode, br.Deskripsi, br.nm_brg, do.qty', 'barang as br', 'br.kd_brg = do.kd_brg', "do.no_order = '".$row['no_order']."'");
								foreach($detail as $rows){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=$rows['barcode']==$rows['kd_brg']?$rows['Deskripsi']:$rows['barcode']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td class="text-center"><?=(int)$rows['qty']?></td>
									</tr>
								<?php
								$total = $total + (int)$rows['qty'];
								} ?>
							</tbody>
                            <tfoot>
                            <tr>
                                <th class="text-left" colspan="4">TOTAL</th>
                                <th class="text-center"><?=$total?></th>
                            </tr>
                            </tfoot>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php } ?>

<script>
	function delete_trans(kode) {
		if (confirm('Akan menghapus data?')) {
			var table_ = ['Master_Mutasi', 'Det_Mutasi', 'Kartu_stock'];
			var condition_ = ['no_faktur_mutasi=\''+kode+'\'','no_faktur_mutasi=\''+kode+'\'','kd_trx=\''+kode+'\''];

			if (otorisasi('branch', {table: table_, condition: condition_})) {
                $.ajax({
                    url: "<?php echo base_url() . 'site/delete_ajax_trx' ?>",
                    type: "POST",
                    data: {table: table_, condition: condition_},
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
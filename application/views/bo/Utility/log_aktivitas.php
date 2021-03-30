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
                            </div>
                            <?=form_close()?>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                        <table id="" class="table table-small-font table-striped table-bordered">
                                            <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Tanggal</th>
                                                <th>Nama User</th>
                                                <th>Tabel</th>
                                                <th>Aktivitas</th>
                                                <th>Aksi</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0);
                                            foreach($report as $row){ $no++; ?>
                                                <tr>
                                                    <td><?=$no?></td>
                                                    <td><?=substr($row['Tgl'],0,19)?></td>
                                                    <td><?=$row['nm_kasir']?></td>
                                                    <td><?=$row['status']?></td>
                                                    <td><?=$row['Aktivitas']?></td>
                                                    <td><button onclick="detail('<?=$row['Tgl']?>')" class="btn btn-primary btn-sm">Detail</button></td>
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
<div id="modal_detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
            </div>
            <div class="modal-body">
                <p>Data Sebelum</p>
                <div class="table-responsive" style="border: none">
                    <table class="table table-bordered">
                        <thead id="sebelum"></thead>
                    </table>
                </div>
                <p>Data Sesudah</p>
                <div class="table-responsive" style="border: none">
                    <table class="table table-bordered">
                        <thead id="sesudah"></thead>
                    </table>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php /*$i =  0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0); foreach($report as $row){ $i++; ?>
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
							<div class="col-sm-3"><b>Tanggal</b></div><div class="col-sm-9"><b> : </b><?=substr($row['tgl'],0,10)?></div>
							<div class="col-sm-3"><b>No. Nota</b></div><div class="col-sm-9"><b> : </b><?=$row['kd_trx']?></div>
							<div class="col-sm-3"><b>Lokasi</b></div><div class="col-sm-9"><b> : </b><?=$row['Lokasi']?></div>
						</div>
						<div class="col-sm-2"></div>
						<div class="col-sm-4">
							<div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><?=$row['kd_kasir']?></div>
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
									<th>Nama Barang</th>
									<th>Open Price</th>
									<th>Harga Jual</th>
									<th>Qty</th>
									<th>Diskon</th>
									<th>Sub Total</th>
								</tr>
								</thead>
								<tbody>
								<?php $no = 0;
								$detail = $this->m_crud->read_data("Det_Trx dt, barang br", "dt.*, br.nm_brg, br.satuan", "dt.kd_brg=br.kd_brg AND dt.kd_trx = '".$row['kd_trx']."'");
								foreach($detail as $rows){ $no++; ?>
									<tr>
										<td><?=$no?></td>
										<td><?=$rows['kd_brg']?></td>
										<td><?=$rows['nm_brg']?></td>
										<td class="text-center"><img width="25px" src="<?=base_url().'assets/images/status-'.($rows['open_price']=='1'?'Y':'T').'.png'?>" /></td>
										<td class="text-right"><?=number_format($rows['hrg_jual'])?></td>
										<td class="text-right"><?=number_format($rows['qty']).' '.$rows['satuan']?></td>
										<td class="text-right"><?=number_format($rows['dis_persen'])?></td>
										<td class="text-right"><?=number_format($rows['hrg_jual']*$rows['qty']-$rows['dis_persen'])?></td>
									</tr>
								<?php } ?>
								</tbody>
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
<?php } */ ?>

<script>

	function detail(param){
		$.ajax({
			url : "<?=base_url().'site/detail_activity'?>",
			type : "POST",
			dataType : "JSON",
			data : {param:param},
			beforeSend: function () {
				$('#loading').show();
			},
			complete: function () {
				$('#loading').hide();
			},
			success : function(res){
				console.log(res);
				$("#modal_detail").modal("show");
				$("#sebelum").html(res.sebelum);
				$("#sesudah").html(res.sesudah);

			}
		});
	}

	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}

	function delete_trans(kode) {
		if (confirm('Akan menghapus data?')) {
			var table_ = ['Master_Trx', 'Det_Trx', 'Kartu_stock'];
			var condition_ = ['kd_trx=\''+kode+'\'','kd_trx=\''+kode+'\'','kd_trx=\''+kode+'\''];

			if (otorisasi('penjualan', {table: table_, condition: condition_})) {
				$.ajax({
					url: "<?php echo base_url() . 'site/delete_ajax_trx' ?>",
					type: "POST",
					data: {table: table_, condition: condition_},
					success: function (res) {
						if (res == true) {
							alert("Data berhasil dihapus!");
						} else {
							alert("Data gagal dihapus!");
						}
						location.reload();
					}
				});
			}
		}
	}
</script>
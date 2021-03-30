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
									<div class="table-responsive">
										<table id="" class="table table-small-font table-striped table-bordered">
											<thead>
											<tr>
												<th style="width: 1%">No</th>
                                                <th style="width: 1%">Aksi</th>
                                                <th>Transaksi</th>
                                                <th>Jenis</th>
                                                <th>Kode Transaksi</th>
                                                <th>Tanggal</th>
                                                <th>Nama User</th>
											</tr>
											</thead>
											<tbody>
											<?php $no = 0 + (($this->uri->segment(4)!=null)?(($this->uri->segment(4)-1) * 30):0);
											foreach($report as $row){ $no++; ?>
												<tr>
													<td><?=$no?></td>
													<td><button onclick="detail('<?=$row['id_log']?>')" class="btn btn-primary btn-sm">Detail</button></td>
													<td><?=$row['transaksi']?></td>
													<td><?=$row['jenis']?></td>
													<td><?=$row['kd_trx']?></td>
													<td><?=date('Y-m-d H:i:s', strtotime($row['tanggal']))?></td>
													<td><?=$row['id_user']?></td>
												</tr>
											<?php } ?>
											</tbody>
											<!--
											<tfoot>
											<tr>
												<th colspan="7">TOTAL PER PAGE</th>
												<th></th>
											</tr>
											<tr>
												<th colspan="7">TOTAL</th>
												<th></th>
											</tr>
											</tfoot>
											-->
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
                <div class="row">
                    <div class="col-sm-6" id="master_head">
                    </div>
                </div>
                <hr>
                <div class="table-responsive" style="border: none">
                    <table class="table table-bordered" id="datatables">
                        <thead id="head"></thead>
                        <tbody id="list"></tbody>
                    </table>
                </div>
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

    function detail(id) {
        $.ajax({
            url: "<?=base_url().'utility/detail_log'?>",
            type: "POST",
            data: {id: id},
            dataType: "JSON",
            beforeSend: function () {
                $('#loading').show();
            },
            complete: function () {
                $('#loading').hide();
            },
            success: function (res) {
                $("#master_head").html(res.master);
                $("#head").html(res.head);
                $("#list").html(res.list);
                $("#datatables").dataTable();
                $("#modal_detail").modal('show');
            }
        })
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
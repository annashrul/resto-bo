<style>
	.bootstrap-datetimepicker-widget tr:hover {
		background-color: #808080;
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
							<?php isset($_GET['pr'])?$update='?pr='.$_GET['pr']:$update=null; ?>
							<div class="row">
                                <div class="col-md-3">
                                    <label>Bulan Lalu</label>
                                    <?php $field = 'tanggal2';?>
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker_back_month" placeholder="" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date('Y-m', strtotime('-1 month', strtotime(date("Y-m")))))?>">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                    </div>
                                </div>
								<div class="col-md-3">
                                    <label>Bulan Sekarang</label>
                                    <?php $field = 'tanggal';?>
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker_back_month" placeholder="" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y-m"))?>">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                    </div>
                                </div>
								<div class="col-md-1">
                                    <label>&nbsp;</label>
                                    <button class="btn btn-primary waves-effect waves-light" onclick="search('search'); return false;" name="search">Search</button>
                                </div>
								<div class="col-md-1">
									&nbsp;<label>&nbsp;</label>
									<button class="btn btn-primary waves-effect waves-light" onclick="search('export'); return false;" name="to_excel">Export</button>
								</div>
							</div>
						</div>

						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
									<table class="table table-striped table-bordered">
										<thead>
										<tr>
											<th style="width: 10px">No</th>
											<th>Lokasi</th>
											<th>Omset Bulan Lalu</th>
											<th>Transaksi Bulan Lalu</th>
											<th>Rata2 Transaksi Bulan Lalu</th>
											<th>Omset Bulan Sekarang</th>
											<th>Transaksi Bulan Sekarang</th>
											<th>Rata2 Transaksi Bulan Sekarang</th>
											<th>Pertumbuhan</th>
											<th>Persentase Pertumbuhan</th>
											<th>Pilihan</th>
										</tr>
										</thead>
										<tbody id="list_omset">
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div> <!-- End Row -->

		</div> <!-- container -->

	</div> <!-- content -->

</div>

<div id="det_omset" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="head_title" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="head_title"></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-3"><b>Periode Sekarang</b></div><div class="col-sm-9"><b> : </b><b id="det_tgl"></b></div>
                        <div class="col-sm-3"><b>Lokasi</b></div><div class="col-sm-9"><b> : </b><b id="det_lokasi"></b></div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-sm-6">
                        <h3>Periode Bulan Lalu</h3>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th colspan="4" class="text-center">Top 100 Items By Qty</th>
                            </tr>
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama</th>
                                <th>Qty</th>
                            </tr>
                            </thead>
                            <tbody id="list_brg_qty_l"></tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <h3>Periode Sekarang</h3>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th colspan="4" class="text-center">Top 100 Items By Qty</th>
                            </tr>
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama</th>
                                <th>Qty</th>
                            </tr>
                            </thead>
                            <tbody id="list_brg_qty"></tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <h3>Periode Bulan Lalu</h3>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th colspan="4" class="text-center">Top 100 Items By Value</th>
                            </tr>
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama</th>
                                <th>Value</th>
                            </tr>
                            </thead>
                            <tbody id="list_brg_value_l"></tbody>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <h3>Periode Sekarang</h3>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th colspan="4" class="text-center">Top 100 Items By Value</th>
                            </tr>
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama</th>
                                <th>Value</th>
                            </tr>
                            </thead>
                            <tbody id="list_brg_value"></tbody>
                        </table>
                    </div>
                </div>
                <!--<div class="row">
                    <div class="col-sm-6">
                        <h3>Periode Bulan Lalu</h3>
                        <div class="col-sm-6">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th colspan="3" class="text-center">Top 100 Supplier By Qty</th>
                                </tr>
                                <tr>
                                    <th>No</th>
                                    <th>Supplier</th>
                                    <th>Qty</th>
                                </tr>
                                </thead>
                                <tbody id="list_supp_qty_l"></tbody>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th colspan="3" class="text-center">Top 100 Supplier By Value</th>
                                </tr>
                                <tr>
                                    <th>No</th>
                                    <th>Supplier</th>
                                    <th>Value</th>
                                </tr>
                                </thead>
                                <tbody id="list_supp_value_l"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Periode Sekarang</h3>
                        <div class="col-sm-6">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th colspan="3" class="text-center">Top 100 Supplier By Qty</th>
                                </tr>
                                <tr>
                                    <th>No</th>
                                    <th>Supplier</th>
                                    <th>Qty</th>
                                </tr>
                                </thead>
                                <tbody id="list_supp_qty"></tbody>
                            </table>
                        </div>
                        <div class="col-sm-6">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th colspan="3" class="text-center">Top 100 Supplier By Value</th>
                                </tr>
                                <tr>
                                    <th>No</th>
                                    <th>Supplier</th>
                                    <th>Value</th>
                                </tr>
                                </thead>
                                <tbody id="list_supp_value"></tbody>
                            </table>
                        </div>
                    </div>
                </div>-->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- ============================================================== -->
<!-- End Right content here -->
<!-- ============================================================== -->
<script type="text/javascript">
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}

	function detail(kode, param=null) {
        var tanggal = $("#tanggal").val();
		var tanggal2 = $("#tanggal2").val();
        if (param == null) {
            $.ajax({
                url: "<?=base_url()?>penjualan/det_omset_periode/" + btoa(kode) + "/" + btoa(tanggal) + "/" + btoa(tanggal2),
                type: "GET",
                dataType: "JSON",
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $("#loading").hide();
                },
                success: function (res) {
                    $("#det_tgl").text(tanggal);
                    $("#det_lokasi").text(kode);
                    $("#list_brg_qty").html(res.list_brg_qty);
                    $("#list_brg_value").html(res.list_brg_value);
                    $("#list_brg_qty_l").html(res.list_brg_qty_l);
                    $("#list_brg_value_l").html(res.list_brg_value_l);
                    /*$("#list_supp_qty").html(res.list_supp_qty);
                    $("#list_supp_value").html(res.list_supp_value);
                    $("#list_supp_qty_l").html(res.list_supp_qty_l);
                    $("#list_supp_value_l").html(res.list_supp_value_l);*/
                    $("#det_omset").modal('show');
                },
                error: function () {
                    alert("EROR");
                }
            });
        } else if(param=='export'){
            window.open("<?php echo base_url() . 'penjualan/det_omset_periode/' ?>"+btoa(kode)+"/"+btoa(tanggal)+"/"+btoa(tanggal2)+"/export",'_blank');
        }
    }

	function search(param) {
        var tanggal = $("#tanggal").val();
        var tanggal2 = $("#tanggal2").val();

        if (param == 'search') {
            $.ajax({
                url: "<?=base_url().'penjualan/get_omset_periode/'?>" + btoa(param) + "/" + btoa(tanggal) + "/" + btoa(tanggal2) ,
                type: "POST",
				data:{tanggal:btoa(tanggal),tanggal2:btoa(tanggal2)},
                dataType: "JSON",
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $("#loading").hide();
                },
                success: function (res) {
                    $("#list_omset").html(res.result);
                },
                error: function () {
                    alert("EROR");
                }
            });
        } else {
            window.open("<?=base_url().'penjualan/get_omset_periode/'?>" + btoa(param) + "/" + btoa(tanggal) + "/" + btoa(tanggal2));
        }
    }
</script>
<style>
	.font_head {
		font-size: 10pt !important;
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
                                        <label>Supplier</label>
                                        <?php $field = 'supplier';
                                        $option = null; $option[''] = 'Semua Supplier';
                                        //$option['all'] = 'All';
                                        $data_option = $this->m_crud->read_data('Supplier', 'Kode, Nama', null, 'Nama asc');
                                        foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
                                        echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
                                        ?>
                                    </div>
                                </div>
								<!--<div class="col-sm-3">
									<div class="form-group">
										<label>Lokasi</label>
										<?php /*$field = 'lokasi';
										$option = null; $option[''] = 'Semua Lokasi';
										//$option['all'] = 'All';
										$data_option = $this->m_crud->read_data('Lokasi', 'Kode, Nama', null, 'Nama asc');
										foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										*/?>
									</div>
								</div>-->
								<!--<div class="col-sm-3">
									<div class="form-group">
										<label>Search</label>
										<?php /*$field = 'any'; */?>
										<input class="form-control" type="text" id="<?/*=$field*/?>" name="<?/*=$field*/?>" value="<?/*=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)*/?>" autofocus />
										<?/*=form_error($field, '<div class="error" style="color:red;">', '</div>')*/?>
									</div>
								</div>-->
								<div class="col-sm-1">
									<label>&nbsp;</label>
									<button type="button" class="btn btn-primary waves-effect waves-light" onclick="get_budget()" name="search">Search</button>
								</div>
								<div class="col-sm-1">
									<label>&nbsp;</label>
									<button type="button" class="btn btn-primary waves-effect waves-light" onclick="get_budget('export')" name="to_excel">Export</button>
								</div>
							</div>
							<?=form_close()?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12" id="table-resposive">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-condensed">
                                            <thead>
                                            <tr>
                                                <th class="font_head" rowspan="2" style="width: 10px">No</th>
                                                <th class="font_head" rowspan="2">Pilihan</th>
                                                <th class="font_head" rowspan="2">Kode</th>
                                                <th class="font_head" rowspan="2">Supplier</th>
                                                <th class="text-center font_head" colspan="2">Current Stock</th>
                                                <th class="text-center font_head" colspan="2">Receive</th>
                                                <th class="text-center font_head" colspan="5">Sales</th>
                                                <th class="font_head" rowspan="2">Qty Rasio</th>
                                                <th class="font_head" rowspan="2">Val Rasio</th>
                                            </tr>
                                            <tr>
                                                <th class="font_head">Qty</th>
                                                <th class="font_head">Value</th>
                                                <th class="font_head">Qty</th>
                                                <th class="font_head">Value</th>
                                                <th class="font_head">Qty</th>
                                                <th class="font_head">Val Cost</th>
                                                <th class="font_head">Val Price</th>
                                                <th class="font_head">Margin Rp</th>
                                                <th class="font_head">Margin %</th>
                                            </tr>
                                            </thead>
                                            <tbody id="list_master_budget">
                                            </tbody>
                                            <tfoot id="total_master_budget">
                                            </tfoot>
                                        </table>
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

<div id="detail_budget" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="detail_budget" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Detail <?=$title?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-4"><b>Kode Supplier</b></div><div class="col-sm-8"><b> : </b><b id="kd_supp"></b></div>
                        <div class="col-sm-4"><b>Nama Supplier</b></div><div class="col-sm-8"><b> : </b><b id="nm_supp"></b></div>
                    </div>
                    <div class="col-sm-6">
                        <button class="btn btn-primary btn-sm pull-right" id="export_detail" name="export_detail"
                                onclick="detail_budget(null, 'export')">Export
                        </button>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th class="font_head" rowspan="2" style="width: 10px">No</th>
                                    <th class="font_head" rowspan="2">Kode Barang</th>
                                    <th class="font_head" rowspan="2">Barcode</th>
                                    <th class="font_head" rowspan="2">Nama Barang</th>
                                    <th class="text-center font_head" colspan="2">Current Stock</th>
                                    <th class="text-center font_head" colspan="2">Receive</th>
                                    <th class="text-center font_head" colspan="5">Sales</th>
                                    <th class="font_head" rowspan="2">Qty Rasio</th>
                                    <th class="font_head" rowspan="2">Val Rasio</th>
                                </tr>
                                <tr>
                                    <th class="font_head">Qty</th>
                                    <th class="font_head">Value</th>
                                    <th class="font_head">Qty</th>
                                    <th class="font_head">Value</th>
                                    <th class="font_head">Qty</th>
                                    <th class="font_head">Val Cost</th>
                                    <th class="font_head">Val Price</th>
                                    <th class="font_head">Margin Rp</th>
                                    <th class="font_head">Margin %</th>
                                </tr>
                                </thead>
                                <tbody id="list_budget">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">Close</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    function get_budget(param = null) {
        if (param == null) {
            $.ajax({
                url: "<?=base_url() . 'pembelian/get_list_budget'?>",
                type: 'POST',
                data: {
                    search: '',
                    any: '',
                    'field-date': $("#field-date").val(),
                    lokasi: $("#lokasi").val(),
                    supplier: $("#supplier").val()
                },
                dataType: "JSON",
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $('#loading').hide();
                },
                success: function (res) {
                    $("#list_master_budget").html(res.list_budget);
                    $("#total_master_budget").html(res.total_budget);
                }
            });
        } else {
            window.open("<?php echo base_url() . 'pembelian/get_list_budget/export' ?>",'_blank');
        }
    }

    function export_by_lokasi(kode) {
        var date = $("#field-date").val();

        window.open("<?php echo base_url() . 'pembelian/export_budget_location/' ?>"+btoa(kode)+"/"+btoa(date)+"",'_blank');
    }

    function detail_budget(kode, param = null) {
        var periode = $("#field-date").val();
        var lokasi = $("#lokasi").val();

        if (lokasi == '') {
            lokasi = '-';
        }

        if (param != 'export') {
            $.ajax({
                url: "<?php echo base_url() . 'pembelian/detail_budget/' ?>" + btoa(kode) + "/" + btoa(periode) + "/" + btoa(lokasi),
                type: "GET",
                dataType: "JSON",
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $('#loading').hide();
                },
                success: function (res) {
                    $("#kd_supp").text(res.kode);
                    $("#nm_supp").text(res.nama);
                    $("#list_budget").html(res.list_barang);
                    $("#detail_budget").modal('show');
                }
            });
        } else {
            window.open("<?php echo base_url() . 'pembelian/detail_budget/' ?>" + btoa(document.getElementById('kd_supp').innerHTML) + "/" + btoa(periode) + "/" + btoa(lokasi) + "/" + btoa('export'),'_blank' );
        }
    }

	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
</script>
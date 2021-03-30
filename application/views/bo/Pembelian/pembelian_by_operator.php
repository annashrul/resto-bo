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
									<div class="form-group">
										<label>Tanggal Periode</label>
										<?php $field = 'tgl_periode'; ?>
										<div class="input-group">
											<input type="text" class="form-control datepicker_date" readonly placeholder="" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y-m-d"))?>">
											<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
										</div>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Lokasi</label>
										<?php $field = 'lokasi';
										$option[''] = 'All';
										$data_option = $this->m_crud->read_data('Lokasi', 'Kode, nama_toko Nama', $this->where_lokasi, 'Nama asc');
										foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
										echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field));
										?>
									</div>
								</div>
								<div class="col-sm-1">
									<label>&nbsp;</label>
									<button type="submit" class="btn btn-primary waves-effect waves-light" onclick="get_data(); return false;" name="search">Search</button>
								</div>
								<div class="col-sm-1" id="export" style="display: none">
									<label>&nbsp;</label>
									<a href="<?=base_url().$content.'/export'?>" target="_blank" class="btn btn-primary waves-effect waves-light">Export</a>
								</div>
								<!--<div class="col-sm-1">
									<label>&nbsp;</label>
                                    <a class="btn btn-primary waves-effect waves-light" href="<?/*=base_url().strtolower($this->control).'/'.$page.'/print/'.base64_encode(json_encode(array('tgl_periode' => isset($this->session->search['tgl_periode'])?$this->session->search['tgl_periode']:'', 'lokasi' => isset($this->session->search['lokasi'])?$this->session->search['lokasi']:'')))*/?>" target="_blank">Export</a>
								</div>-->
							</div>
							<?=form_close()?>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-md-12 col-sm-12 col-xs-12">
									<div class="row" id="list_kasir">
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
    <div class="modal-dialog modal-full">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">Detail Pembelian</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="col-sm-4"><b>Tanggal</b></div><div class="col-sm-8"><b> : </b><b id="tgl_detail"></b></div>
                        <div class="col-sm-4"><b>Operator</b></div><div class="col-sm-8"><b> : </b><b id="operator_detail"></b></div>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <table class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>No. Transaksi</th>
                                <th>Nota Supplier</th>
                                <th>Type</th>
                                <th>Pelunasan</th>
                                <th>Supplier</th>
                                <th>Lokasi</th>
                                <th>Penerima</th>
                                <th>Diskon</th>
                                <th>PPN</th>
                                <th>Total Pembelian</th>
                            </tr>
                            </thead>
                            <tbody id="list_data">
                            </tbody>
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

<script>
    function detail(operator) {
        $.ajax({
            url: "<?=base_url().$content.'/detail'?>",
            type: "POST",
            data: {operator:operator},
            dataType: "JSON",
            beforeSend: function () {
                $('#loading').show();
            },
            complete: function () {
                $('#loading').hide();
            },
            success: function (res) {
                $("#list_data").html(res.list);
                $("#tgl_detail").text(res.tgl);
                $("#operator_detail").text(res.operator);
                $("#modal_detail").modal('show');
            }
        });
    }

    function get_data() {
        var tgl = $("#tgl_periode").val();
        var lokasi = $("#lokasi").val();
        var list = '';

        $.ajax({
            url: "<?=base_url().$content.'/get_data'?>",
            type: "POST",
            data: {tgl: tgl, lokasi: lokasi},
            dataType: "JSON",
            beforeSend: function () {
                $('#loading').show();
            },
            complete: function () {
                $('#loading').hide();
            },
            success: function (res) {
                if (res.status) {
                    $("#export").show();
                    var result = res.data;
                    for (var i = 0; i < result.length; i++) {
                        list += '<div class="col-lg-4">' +
                            '<div class="panel panel-border panel-info">' +
                            '<div class="panel-heading">' +
                            '<h3 class="panel-title">' + result[i].nama + '</h3>' +
                            '<div class="input-inline"><a href="javascript:" onclick="detail(\'' + result[i].operator + '\')" class="btn btn-primary btn-sm" style="margin-right: 5px"><spna class="fa fa-list"></span></a><a href="<?=base_url().$content.'/to_pdf/'?>' + btoa(result[i].operator) + '" target="_blank" class="btn btn-primary btn-sm"><spna class="fa fa-download"></span></a></div>' +
                            '</div>' +
                            '<div class="panel-body">' +
                            '<table width="100%" border="0">' +
                            '<tr>' +
                            '<td>Jumlah Transaksi</td>' +
                            '<th class="pull-right">' + parseInt(result[i].trx) + '</th>' +
                            '</tr>' +
                            '<tr>' +
                            '<td>Qty Produk</td>' +
                            '<th class="pull-right">' + parseInt(result[i].qty) + '</th>' +
                            '</tr>' +
                            '<tr>' +
                            '<td>Qty Bonus</td>' +
                            '<th class="pull-right">' + parseInt(result[i].bonus) + '</th>' +
                            '</tr>' +
                            '<tr>' +
                            '<td>Diskon</td>' +
                            '<th class="pull-right">' + to_rp(result[i].disc, '-') + '</th>' +
                            '</tr>' +
                            '<tr>' +
                            '<td>Pajak</td>' +
                            '<th class="pull-right">' + to_rp(result[i].ppn, '-') + '</th>' +
                            '</tr>' +
                            '<tr>' +
                            '<td>Total Pembelian</td>' +
                            '<th class="pull-right">' + to_rp((result[i].total-result[i].disc+result[i].ppn).toFixed(2)) + '</th>' +
                            '</tr>' +
                            '</table>' +
                            '</div>' +
                            '</div>' +
                            '</div>';
                    }
                }

                $("#list_kasir").html(list);
            }
        });
    }
</script>
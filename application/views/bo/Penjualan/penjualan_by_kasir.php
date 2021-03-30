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
											<input type="text" class="form-control datepicker_date" placeholder="" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:(set_value($field)?set_value($field):date("Y-m-d"))?>">
											<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
										</div>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Lokasi</label>
										<?php $field = 'lokasi';
										$option['all'] = 'All';
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

<script>
    function get_data() {
        var tgl = $("#tgl_periode").val();
        var lokasi = $("#lokasi").val();

        $.ajax({
            url: "<?=base_url().'utility/report_kasir'?>",
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
                    $("#list_kasir").html(res.list);
                } else {
                    $("#list_kasir").html('');
                }
            }
        });
    }

    function re_closing(id, tgl) {
    	// console.log($("#tanggal").val());
        if (confirm('Akan melakukan closing ulang?')) {
            $.ajax({
                url: "<?=base_url() . 'utility/re_closing'?>",
                type: "POST",
                data: {id: id, tgl: tgl},
                dataType: "JSON",
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $('#loading').hide();
                },
                success: function (res) {
                    get_data();
                },
            })
        }
    }
</script>
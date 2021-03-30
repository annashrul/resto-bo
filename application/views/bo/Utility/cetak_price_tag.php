<style>
    th, td {
        font-size: 9pt;
    }

    .form-control {
        font-size: 9pt;
    }
</style>
<!-- Page-Title -->
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

            <!-- Main Content -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
							<?= form_open('utility/cetak_price_tag', array('id'=>'form_list_pt')); ?>
                            <div class="row" style="margin-bottom: 3px">
                                <div class="col-sm-2">
									<label>Lokasi Barang</label>
									<?php $field = 'lokasi_barang'; ?>
									<?php $option = null;
									$option[''] = '-- Pilih --';
									//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
									foreach($data_lokasi as $row){ $option[$row['Kode']] = $row['Nama']; }
									echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'onchange'=>"add_tmp_master(); hide_notif('alr_".$field."');")); 
									?>
                                    <b class="error" id="alr_<?=$field?>"></b>
                                </div>
								<div class="col-sm-3">
									<label><?=$menu_group['as_group1']?></label>
									<?php $field = 'group1'; ?>
									<?php $option = null;
									$option[''] = '-- Pilih --';
									//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
									foreach($data_group1 as $row){ $option[$row['Kode']] = $row['Kode'].' | '.$row['Nama']; }
									echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'onchange'=>"add_tmp_master(); hide_notif('alr_".$field."');")); 
									?>
                                    <b class="error" id="alr_<?=$field?>"></b>
                                </div>
								<div class="col-sm-3">
									<label><?=$menu_group['as_group2']?></label>
									<?php $field = 'group2'; ?>
									<?php $option = null;
									$option[''] = '-- Pilih --';
									//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
									foreach($data_group2 as $row){ $option[$row['Kode']] = $row['Kode'].' | '.$row['Nama']; }
									echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'onchange'=>"add_tmp_master(); hide_notif('alr_".$field."');")); 
									?>
                                    <b class="error" id="alr_<?=$field?>"></b>
                                </div>
								<div class="col-sm-3">
									<label>Kelompok</label>
									<?php $field = 'kel_brg'; ?>
									<?php $option = null;
									$option[''] = '-- Pilih --';
									//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
									foreach($data_kel_brg as $row){ $option[$row['kel_brg']] = $row['kel_brg'].' | '.$row['nm_kel_brg']; }
									echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'onchange'=>"add_tmp_master(); hide_notif('alr_".$field."');")); 
									?>
                                    <b class="error" id="alr_<?=$field?>"></b>
                                </div>
								<div class="col-sm-3">
									<div class="form-group">
										<label>Search</label>
										<div class="input-group">
											<div class="input-group-btn">
												<?php $field = 'column';
												$option = null;
												$option['br.kd_brg'] = 'Kode Barang';
												$option['br.barcode'] = 'Barcode';
												//$data_option = $this->m_crud->read_data('Group2', 'Kode, Nama', null, 'Nama asc');
												//foreach($data_option as $row){ $option[$row['Kode']] = $row['Nama']; }
												echo form_dropdown($field, $option, isset($this->session->search[$field])?$this->session->search[$field]:set_value($field), array('class' => 'select2', 'id'=>$field, 'required'=>'required')); 
												?>
											</div>
											<?php $field = 'any'; ?>
											<input class="form-control" style="height: 40px" type="text" id="<?=$field?>" name="<?=$field?>" value="<?=isset($this->session->search[$field])?$this->session->search[$field]:set_value($field)?>" autofocus />
										</div>
										<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
									</div>
								</div>
								<div class="col-sm-2">
									<label>Harga Baru</label>
									<?php $field = 'harga_baru'; ?>
									<div class="checkbox checkbox-primary">
										<input id="<?=$field?>" name="<?=$field?>" value="1" type="checkbox" <?=(isset($this->session->search[$field]) && $this->session->search[$field]=='1')?'checked':''?>>
										<label for="<?=$field?>" title="Semua Periode" style="font-size: 7.9pt">
											Harga Baru
										</label>
									</div>
                                    <b class="error" id="alr_<?=$field?>"></b>
                                </div>
								<div class="col-sm-1">
									<label>&nbsp;</label>
									<!--<button class="btn btn-primary" id="search" name="search" type="button" onclick="modal_tampil_barang();">Tampilkan</button>-->
									<button class="btn btn-primary" id="search" name="search" type="submit">Tampilkan</button>
                                </div>
                            </div>
							<hr>
							<!--<div class="row">
                                <div class="col-md-4">
                                    <div class="panel-heading">
                                        <div class="input-group">
                                            <select class="form-control" id="cat_cari" name="cat_cari">
                                                <option value="1">Kode Barang</option>
                                                <option value="2">Barcode</option>
                                                <option value="3"><?=$menu_group['as_deskripsi']?></option>
                                                <!--<option value="4">Nama Barang</option>--
                                            </select>
                                        </div>
                                        <input type="text" class="form-control" id="barcode" name="barcode">
                                        <b class="error" id="alr_barang"></b>
                                    </div>
                                </div>
                            </div>-->
							<div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
												<tr>
													<th style="width: 10px">No</th>
													<th>
														<div class="checkbox checkbox-primary">
															<input class="form-control" type="checkbox" id="check_all" name="check_all" />	
															<label for="check_all"> </label>
														</div>
													</th>
													<th>Kode Barang</th>
													<th>Barcode</th>
													<th>Nama Barang</th>
													<th><?=$menu_group['as_deskripsi']?></th>
													<th><?=$menu_group['as_group1']?></th>
													<th><?=$menu_group['as_group2']?></th>
													<th>Kelompok</th>
													<th>Harga Jual</th>
													<!--<th>Qty</th>-->
												</tr>
                                            </thead>
                                            <tbody id="list_barang">
												<?php $no=0; foreach($barang as $row){ $no++; ?>
													<tr>
														<th style="width: 10px"><?=$no?></th>
														<th>
															<div class="checkbox checkbox-primary">
																<input class="form-control check_all" type="checkbox" id="check<?=$no?>" name="check<?=$no?>" value="1" />	
																<label for="check"> </label>
															</div>
														</th>
														<th><?=$row['kd_brg']?></th>
														<th><?=$row['barcode']?></th>
														<th><?=$row['nm_brg']?></th>
														<th><?=$row['deskripsi']?></th>
														<th><?=$row['nama_group1']?></th>
														<th><?=$row['nama_group2']?></th>
														<th><?=$row['nama_kelompok']?></th>
														<th><?=number_format($row['hrg_jual_1'])?></th>
													</tr>
													<input type="hidden" id="kd_brg<?=$no?>" name="kd_brg<?=$no?>" value="<?=$row['kd_brg']?>" />
													<input type="hidden" id="nm_brg<?=$no?>" name="nm_brg<?=$no?>" value="<?=$row['nm_brg']?>" />
													<input type="hidden" id="barcode<?=$no?>" name="barcode<?=$no?>" value="<?=$row['barcode']?>" />
													<input type="hidden" id="deskripsi<?=$no?>" name="deskripsi<?=$no?>" value="<?=$row['deskripsi']?>" />
													<input type="hidden" id="hrg_jual_1<?=$no?>" name="hrg_jual_1<?=$no?>" value="<?=$row['hrg_jual_1']?>" />
												<?php } ?>
												<input type="hidden" id="jumlah" name="jumlah" value="<?=$no?>" />
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
							<?=form_close()?>
                            <div class="row">
                                <div class="col-md-7">
                                    <button class="btn btn-primary" onclick="if (confirm('Akan mencetak price tag?')){cetak_barcode()}" id="simpan" type="button">Cetak Price Tag</button>
                                    <button class="btn btn-primary" onclick="if (confirm('Akan membatalkan transaksi?')){batal_transaksi()}" id="batal" type="button">Batal</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal-container" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modal-label"></h4>
            </div>
			<div class="modal-footer">
				<input onclick="add_barang()" type="button" class="btn btn-primary pull-right" value="Pilih">
			</div>
			<div class="modal-body">
				<div class="row">
                    <div class="col-md-12">
                        <table class="table-bordered table_check" width="100%">
                            <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>Kode Barang</th>
                                <th>Barcode</th>
                                <th>Nama Barang</th>
                                <th><?=$menu_group['as_deskripsi']?></th>
                            </tr>
                            </thead>
                            <tbody id="list_barang_modal"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script>
    $(document).ready(function () {
        /*get master*/
        //get_tmp_master();

        /*get detail*/
        //get_tmp_detail(1);
    });

	/*function modal_tampil_barang(){
		$.ajax({
            url: "<?php echo base_url() . 'utility/list_tampilkan_barang_pt' ?>",
            data: {supplier_: 'supplier'},
            type: "POST",
            dataType: "JSON",
            success: function (data) {
                $("#modal-container").modal('show');
            }
        });
	}*/
	
	$("#check_all").click(function () {
		if ($("#check_all").is(":checked")) {
			$(".check_all").prop('checked', true);
		} else {
			$(".check_all").prop('checked', false);
		}
	});
	
    function get_tmp_master() {
        $.ajax({
            url: "<?php echo base_url() . 'utility/get_tr_temp_m_pt' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#lokasi_barang").select2('val', data['m2']);
            }
        });
    }

    function add_tmp_master() {
        var lokasi = $("#lokasi_barang").val();

        $.ajax({
            url: "<?php echo base_url() . 'utility/add_tr_temp_m_pt' ?>",
            data: {lokasi_:lokasi},
            type: "POST",
            dataType: "JSON"
        });
    }

    function update_tmp_master(column, data) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/update_tr_temp_m_pt/' ?>" + btoa(column) + "/" + btoa(data),
            type: "GET"
        });
    }

    function get_tmp_detail(param = 0) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/get_tr_temp_d_pt' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#list_barang").html(data.list_barang);
                if(param != 1) {
                    $("#barcode").focus();
					//$("#d7" + $("#col").val()).focus().select();
                } else {
                    $("#barcode").focus();
                }
            }
        });
    }

    function to_barcode(event) {
        if (event.keyCode == 13) {
            $("#barcode").focus();
        }
    }

    function to_qty(event, id) {
        if (event.keyCode == 13) {
            $("#d7"+id).focus().select();
        }
    }

    function update_tmp_detail(barcode, column, value) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/update_tr_temp_d_pt/' ?>" + btoa(barcode) + "/" + btoa(column) + "/" + btoa(value),
            type: "GET"
        });
    }

    function hapus_barang(barcode) {
        $.ajax({
            url: "<?php echo base_url() . 'utility/delete_tr_temp_d_pt/' ?>" + btoa(barcode),
            type: "GET",
            success: function (data) {
                if (data) {
                    get_tmp_detail();
                }
            }
        });
    }

    $("#barcode").keyup(function (event) {
        var cat_cari = $("#cat_cari").val();
        var barcode = $("#barcode").val();
        var lokasi_barang = $("#lokasi_barang").val();
        hide_notif("alr_barang");

        if (event.keyCode == 13) {
            if (lokasi_barang == '') {
                $("#alr_lokasi_barang").text("Lokasi wajib dipilih!");
            }

            if (lokasi_barang != '') {
                $.ajax({
                    url: "<?php echo base_url() . 'utility/get_barang_pt/' ?>" + btoa(barcode) + "/" + btoa(cat_cari) + "/" + btoa(lokasi_barang),
                    type: "GET",
                    dataType: "JSON",
                    success: function (data) {
                        if (data.status == 1) {
                            $("#barcode").val("");
                            get_tmp_detail();
                        } else if (data.status == 2) {
                            alert(data.notif);
                            $("#barcode").val("");
                            get_tmp_detail();
                        } else {
                            $("#barcode").val("").focus();
                            $("#alr_barang").text(data.notif)
                        }
                    }
                });
            }
        }
    });

    function batal_transaksi() {
        $.ajax({
            url: "<?php echo base_url().'utility/delete_trans_pt' ?>",
            type: "GET",
            success: function (data) {
                if (data) {
                    location.reload();
                }
            }
        });
    }

    function cetak_barcode() {
		$.ajax({
            url: "<?php echo base_url().'utility/insert_list_pt' ?>",
            type: "POST",
			data: $('#form_list_pt').serialize(),
            success: function (data) {
                if (data) {
                    window.open('<?=base_url().'cetak/cetak_price_tag'?>');
                }
            }
        });
    }
</script>
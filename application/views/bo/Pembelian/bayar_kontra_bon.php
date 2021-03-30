<style>
	th, td {
		font-size: 9pt; 
	}

	.form-control {
		font-size: 9pt;  padding:3px;
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
						<?=form_open($content, array('id'=>'form_tr'))?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="panel-body">
										<div class="row">
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Tanggal</label>
													<div class="col-sm-6">
														<div class="input-group date">
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
															<input class="form-control pull-right datepicker_date_from" readonly onchange="update_tmp_master('m3', $(this).val())" name="tgl" id="tgl" type="text" value="<?=date("Y-m-d")?>">
															<!--custom_front_date('pembelian', $(this).val())-->
														</div>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Lokasi</label>
													<div class="col-sm-6">
														<?php $field = 'lokasi' ?>
														<?php $option = null; $option[''] = 'Pilih';
														foreach($data_lokasi as $row){ $option[$row['Kode']] = $row['Nama']; }
														echo form_dropdown($field, $option, set_value($field), array('class' => 'select2', 'required'=>'required', 'id'=>$field, 'onclick'=>'validasi(\''.$field.'\');', 'onchange'=>'update_tmp_master(\'m4\', $(this).val());')); ?>
														<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Acc. No.</label>
													<div class="col-sm-6">
														<?php $field = 'acc_no' ?>
														<?php $option = null; $option[''] = 'Pilih';
														echo form_dropdown($field, $option, set_value($field), array('class' => 'select2', 'required'=>'required', 'id'=>$field, 'onclick'=>"validasi('".$field."');", 'onchange'=>'update_tmp_master(\'m8\', $(this).val()); pilih_rekening($(this).val());')); ?>
														<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Bank</label>
													<div class="col-sm-6">
														<?php $field = 'bank' ?>
														<?php $option = null; $option[''] = 'Pilih';
														foreach($data_bank as $row){ $option[$row['Nama']] = $row['Nama']; }
														echo form_dropdown($field, $option, set_value($field), array('class' => 'select2', 'required'=>'required', 'id'=>$field, 'onclick'=>'validasi(\''.$field.'\');', 'onchange'=>'update_tmp_master(\'m9\', $(this).val());')); ?>
														<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Atas Nama</label>
													<div class="col-sm-6">
														<?php $field = 'an' ?>
														<input class="form-control" readonly id="<?=$field?>" name="<?=$field?>" /> 
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">BI Code</label>
													<div class="col-sm-6">
														<?php $field = 'bi_code' ?>
														<input onkeyup="validasi('<?=$field?>');" onblur="update_tmp_master('m10', $(this).val())" class="form-control" id="<?=$field?>" name="<?=$field?>" /> 
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Bank Branch Name</label>
													<div class="col-sm-6">
														<?php $field = 'bank_branch' ?>
														<input onkeyup="validasi('<?=$field?>');" onblur="update_tmp_master('m11', $(this).val())" class="form-control" id="<?=$field?>" name="<?=$field?>" /> 
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Supplier</label>
													<div class="col-sm-6">
														<?php $field = 'supplier' ?>
														<?php $option = null; $option[''] = 'Pilih';
														foreach($data_supplier as $row){ $option[$row['kode']] = $row['kode'].' | '.$row['Nama']; }
														echo form_dropdown($field, $option, set_value($field), array('class' => 'select2', 'required'=>'required', 'id'=>$field, 'onclick'=>"validasi('".$field."'); get_rekening($(this).val()); read_nota_kontra(); hapus_list_kontra(''); $('#nota_kontra').val(''); update_tmp_master('m7', '');", 'onchange'=>'update_tmp_master(\'m5\', $(this).val());')); ?>
														<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">No. Kontra Bon</label>
													<div class="col-sm-6">
														<div class="input-group">
															<?php $field='nota_kontra'; ?>
															<input type="text" class="form-control input-sm" onkeyup="validasi('<?=$field?>'); if(event.keyCode==13){validasi('supplier'); read_nota_kontra($(this).val());}" id="<?=$field?>" name="<?=$field?>" />
															<div class="input-group-btn">
																<button onclick="validasi('supplier'); read_nota_kontra();" type="button" id="cari_barcode" name="cari_barcode" class="btn btn-primary btn-sm"><i class="md md-search"></i></button>
															</div>
														</div>
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Jenis</label>
													<div class="col-sm-6">
														<?php $field = 'jenis' ?>
														<input onkeyup="validasi('<?=$field?>');" onblur="update_tmp_master('m12', $(this).val())" class="form-control" id="<?=$field?>" name="<?=$field?>" /> 
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Rec</label>
													<div class="col-sm-6">
														<?php $field = 'rec' ?>
														<input onkeyup="validasi('<?=$field?>');" onblur="update_tmp_master('m13', $(this).val())" class="form-control" id="<?=$field?>" name="<?=$field?>" /> 
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Receiv</label>
													<div class="col-sm-6">
														<?php $field = 'receiv' ?>
														<input onkeyup="validasi('<?=$field?>');" onblur="update_tmp_master('m14', $(this).val())" class="form-control" id="<?=$field?>" name="<?=$field?>" /> 
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-4">Keterangan</label>
													<div class="col-sm-6">
														<textarea rows="2" onkeyup="validasi('descrip');" onblur="update_tmp_master('m6', $(this).val())" class="form-control" id="descrip" name="descrip"></textarea>
														<b class="error" id="alr_descrip"></b>
													</div>
												</div>
											</div>
										</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="" id="list_nota_beli_kontra">
                                        <!--<table class="table table-striped table-bordered">
                                            <thead>
												<tr>
													<th style="width: 10px;">No</th>
													<th width="50px">Action</th>
													<th>No. Kontra Bon</th>
													<th>Tanggal Kontra Bon</th>
													<th>Tanggal Bayar</th>
													<th>Amount Kontra Bon</th>
													<th>Sudah Bayar</th>
													<th>Bayar Kontra Bon</th>
												</tr>
                                            </thead>
                                            <tbody id="list_nota_beli_kontra">
												
                                            </tbody>
                                        </table>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-7">
                                    <button class="btn btn-primary" onclick="if (confirm('Simpan Transaksi?')){simpan_transaksi()}" id="simpan" type="button">Simpan</button>
                                    <button class="btn btn-primary" onclick="if (confirm('Batalkan Transaksi?')){batal_transaksi()}" id="batal" type="button">Batal</button>
                                    <button class="btn btn-primary" onclick="if (confirm('Tutup Transaksi?')){tutup_transaksi()}" id="keluar" type="button">Tutup</button>
                                    <!--<button class="btn btn-primary" id="arsip_pembelian" type="submit">Arsip Pembelian</button>-->
                                </div>
                                <div class="col-md-5">
                                    <div class="row" style="margin-bottom: 3px">
                                        <label class="col-sm-4">Total</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="total" name="total" class="form-control text-right" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?=form_close()?>
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
			<?= form_open_multipart($content, array('id'=>'form_nota_kontra')) ?>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title" id="modal-label">Supplier : <label id="lab_supplier"></label></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<button style="margin-bottom:5px;" class="btn btn-primary hidden" type="button" onclick="add_list_nota_beli_kontra();" id="add_kontra" name="add_kontra">Pilih</button>
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th style="width: 10px;">No</th>
										<th width="50px" style="text-align:center;">
											Pilih
											<!--<div class="checkbox checkbox-primary">
												<input class="form-control" type="checkbox" id="checklist_nota" name="checklist_nota" />
												<label for="checklist_nota"></label>
											</div>-->
										</th>
										<th>No. Kontra Bon</th>
										<th>Tanggal Kontra Bon</th>
										<th>Tanggal Bayar</th>
										<th>Amount Kontra Bon</th>
									</tr>
								</thead>
								<tbody id="list_nota_kontra">

								</tbody>
							</table>
							
						</div>
					</div>
				</div>
				<div class="modal-footer">

				</div>
			<?= form_close() ?>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    $(document).ready(function () {
        /*get master*/
        get_tmp_master();

        /*get detail*/
        get_tmp_detail();
	}).on("keypress", ":input:not(textarea)", function(event) {
		return event.keyCode != 13;
	});
	
	function get_tmp_master() {
        $.ajax({
            url: "<?= base_url() . $this->control . '/get_tr_temp_m_bkb' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if (data.status == 1) {
					get_rekening(data.temp['m5']);
                    setTimeout(function(){ $("#acc_no").val(data.temp['m8']).change(); }, 1000);
					$('#tgl').datepicker("setDate", data.temp['m3']);
                    $("#descrip").val(data.temp['m6']).change();
					$("#lokasi").val(data.temp['m4']).change();
                    $("#supplier").val(data.temp['m5']).change();
                    $("#nota_kontra").val(data.temp['m7']).change(); 
                    $("#bank").val(data.temp['m9']).change(); 
                    $("#bi_code").val(data.temp['m10']).change(); 
                    $("#bank_branch").val(data.temp['m11']).change(); 
                    $("#jenis").val(data.temp['m12']).change(); 
                    $("#rec").val(data.temp['m13']).change(); 
                    $("#receiv").val(data.temp['m14']).change(); 
					//if(data.temp['m7']!=null && data.temp['m7']!=''){ $('#tgl_bayar').datepicker("setDate", data.temp['m7']); }
                }

            }
        });
    }
	
	function get_tmp_detail() {
        $.ajax({
            url: "<?= base_url() . $this->control . '/get_tr_temp_d_bkb' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#list_nota_beli_kontra").html(data.list_nota_beli_kontra);
                $("#total").val(to_rp(data.total));
            }
        });
    }
	
	function get_rekening(supplier) {
        $.ajax({
            url: "<?= base_url() . $this->control . '/get_rekening/' ?>"+btoa(supplier),
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#acc_no").html(data.list_rekening);
            }
        });
    }
	
	function pilih_rekening(rek){
		$.ajax({
            url: "<?= base_url() . $this->control . '/pilih_rekening/' ?>"+btoa(rek),
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#bank").val(data.data_rekening['bank']).change();
                $("#an").val(data.data_rekening['an']).change();
                $("#bi_code").val(data.data_rekening['bi_code']).change();
                $("#bank_branch").val(data.data_rekening['branch_name']).change();
            }
        });
	}
	
	function update_tmp_master(column, value, trx='') {
        trx = 'BK';
		$.ajax({
			url: "<?= base_url() . $this->control . '/update_tr_temp_m_bkb/' ?>" + btoa(trx) + "/" + btoa(column) + "/" + btoa(value),
			type: "GET"
		});
    }
	
	function update_tmp_detail(id, column, value) {
        $.ajax({
            url: "<?= base_url() . $this->control . '/update_tr_temp_d_bkb/' ?>" + btoa(id) + "/" + btoa(column) + "/" + btoa(value),
            type: "GET"
        });
    }
	
	function read_nota_kontra(nota=''){ 
		$("#lab_supplier").text('');
		$("#alr_nota_kontra").text('');
		var tgl_bayar = $('#tgl').val();
		var supplier = $('#supplier').val();
		if(tgl_bayar!='' && supplier!=''){ 
			var data_supplier = <?=json_encode($data_supplier)?>;
			for(var i=0; i<data_supplier.length; i++){
				if(data_supplier[i]['kode']==supplier){
					$("#lab_supplier").text(data_supplier[i]['kode']+' | '+data_supplier[i]['Nama']);
					//$("#checklist_nota").prop('checked', false)
					$.ajax({
						url: "<?= base_url() . $this->control . '/read_nota_kontra_bkb/' ?>" + btoa(supplier) + "/" + btoa(tgl_bayar) + "/" + btoa(nota),
						type: "GET",
						dataType: "JSON",
						success: function (data) {
							$("#list_nota_kontra").html(data.list_nota_kontra);
							if(nota!=''){ 
								$('#checklist_nota1').prop('checked', true);
								$("#modal-container").modal('hide');
								$('#add_kontra').click();
								$('#nota_kontra').val('');
								if(data.status == 0){ $("#alr_nota_kontra").text('No. Kontra Bon tidak ditemukan'); }
							}
						}
					});
					if(nota==''){ $("#modal-container").modal('show'); }
				}
			}
		} 
	}
	
	$("#checklist_nota").click(function () {
		if ($("#checklist_nota").is(":checked")) {
			$(".checklist_nota").prop('checked', true);
		} else {
			$(".checklist_nota").prop('checked', false);
		} 
	}); 
	
	function add_list_nota_beli_kontra(){
		$.ajax({
			url: "<?= base_url() . $this->control . '/add_list_bayar_kontra' ?>",
			type: "POST",
			data: $('#form_nota_kontra').serialize(),
			success: function(){ 
				$("#modal-container").modal('hide');
				get_tmp_master();
				get_tmp_detail();
				//setTimeout(function(){ $("#article"+id).focus(); }, 500); 
			}
		});
	}
	
	function hapus_list_kontra(id) {
        $.ajax({
            url: "<?= base_url() . $this->control . '/delete_tr_temp_d_bkb/' ?>" + btoa(id),
            type: "GET",
            success: function (data) {
                if (data) {
					if(id==''){
						$("#acc_no").val('').change(); 
						$("#bank").val('').change(); 
						$("#an").val('').change(); 
					}
					get_tmp_detail();
                }
            }
        });
    }
	
	function hitung_kontra(){
		var sub_total=0;
		var total = 0;
		
		for(var i=1; i<=$("#jumlah_nota_beli_kontra").val(); i++){ 
			if($("#bayar_kontrabon"+i).val() != undefined){
				sub_total = hapuskoma($("#bayar_kontrabon"+i).val()); if(sub_total == ''){ sub_total = 0; }
				total = total + parseFloat(sub_total);
			}
		}
		$("#total").val(to_rp(total));
		if(isNaN(parseFloat(total))){ $("#simpan").prop("disabled", true); }
		else { $("#simpan").prop("disabled", false); }
	}
	
	
	function to_col(event, col) {
        if (event.keyCode == 13) {
            $("#"+col).focus().select();
        }

        if (event.ctrlKey && event.keyCode == 13) {
			add_list();
        }
    }
	
	function batal_transaksi() {
        $.ajax({
            url: "<?= base_url() . $this->control . '/delete_trx_bkb' ?>",
            type: "GET",
            success: function (data) {
                location.reload();
            }
        });
    }
	
	function validasi(column='', action=''){
		var value = '';
		var valid = 1;
		
        if(column==''){ column='lokasi'; } 
        if(column == 'lokasi'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("Lokasi harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; } 
		}
		
		if(column==''){ column='tgl_bayar'; } 
        if(column == 'tgl_bayar'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("Tanggal Bayar harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; } 
		}
		
		if(column==''){ column='acc_no'; } 
        if(column == 'acc_no'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("Acc. No. harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; } 
		}
		
		if(column==''){ column='bank'; } 
        if(column == 'bank'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("Bank harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; } 
		}
		
		if(column==''){ column='bi_code'; } 
        if(column == 'bi_code'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("BI Code harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; } 
		}
		
		if(column==''){ column='bank_branch'; } 
        if(column == 'bank_branch'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("Bank Branch Name harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; } 
		}
		
		if(column==''){ column='supplier'; } 
        if(column == 'supplier'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("Supplier harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; } 
		}
		
		if(column==''){ column='jenis'; } 
        if(column == 'jenis'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("Jenis harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; } 
		}
		
		if(column==''){ column='rec'; } 
        if(column == 'rec'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("Rec harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; } 
		}
		
		if(column==''){ column='receiv'; } 
        if(column == 'receiv'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("Receiv harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; } 
		}
		
		if(column==''){ column='descrip'; } 
        if(column == 'descrip'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("Keterangan harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; }
		}
		
		for(var i=1; i<=$("#jumlah_nota_beli_kontra").val(); i++){ 
			if($("#bayar_kontrabon"+i).val() != undefined){
				var nilai_kontrabon = hapuskoma($("#nilai_kontrabon"+i).val()); if(nilai_kontrabon == ''){ nilai_kontrabon = 0; }
				var sudah_bayar = hapuskoma($("#sudah_bayar"+i).val()); if(sudah_bayar == ''){ sudah_bayar = 0; }
				var bayar_kontrabon = parseFloat(nilai_kontrabon) - parseFloat(sudah_bayar);
				
				if(column==''){ column='bayar_kontrabon'+i; } 
				if(column == 'bayar_kontrabon'+i){ 
					value = hapuskoma($("#"+column).val()); 
					if(value == ''){ $("#alr_"+column).text("Bayar Kontrabon harus diisi!"); valid=0; } //else { $("#alr_"+column).text(""); } 
					else if(value <= 0){ $("#alr_"+column).text("Bayar Kontrabon harus lebih dari 0!"); valid=0; } //else { $("#alr_"+column).text(""); } 
					else if(value > bayar_kontrabon){ $("#alr_"+column).text("Maksimal Bayar Kontrabon " + to_rp(bayar_kontrabon)); valid=0; } else { $("#alr_"+column).text(""); } 
					if(action=='save'){ column=''; }
				}
			}
		}
		
		return valid;
	} 
	
    function simpan_transaksi() {
        var valid = validasi('','save');
		var list = 0;
		
		/*for(var i=1; i<=$("#jumlah").val(); i++){ 
			if($("#article"+i).val() != undefined && $("#nama"+i).val() != undefined){
				if($("#article"+i).val()!='' && $("#nama"+i).val()!=''){
					list++;
				} 
			}
		}*/
		list = $("#jumlah_nota_beli_kontra").val();
		
		if(valid==1){
			if(list>0){
				$.ajax({
					url: "<?= base_url().$this->control . '/simpan_bkb' ?>",
					type: "POST",
					data: $('#form_tr').serialize(),
					success: function (data) {
						if (data) {
							alert("Transaction has been saved.");
						} else {
							alert("Transaction failed!");
						}
						location.reload();
					}
				});
			} else {
				alert('List cannot be empty!');
			}
        }
    }
</script>

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
													<label class="col-sm-3">Tanggal</label>
													<div class="col-sm-6">
														<div class="input-group date">
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
															<input class="form-control pull-right datepicker_date_from" readonly onchange="update_tmp_master('m3', $(this).val()); nilai_retur();" name="tgl" id="tgl" type="text" value="<?=date("Y-m-d")?>">
															<!--custom_front_date('pembelian', $(this).val())-->
														</div>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-3">Lokasi</label>
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
													<label class="col-sm-3">Jenis</label>
													<div class="col-sm-6 form-inline">
														<?php $field = 'jenis'; ?>
														<div class="radio radio-primary">
															<input class="form-control" type="radio" onclick="update_tmp_master('m12', $(this).val());" onchange="jenis_kontrabon('ganti');" id="<?=$field?>1" name="<?=$field?>" value="Pembelian" checked />
															<label for="<?=$field?>1"> Pembelian </label>
														</div>
														<div class="radio radio-primary">
															<input class="form-control" type="radio" onclick="update_tmp_master('m12', $(this).val());" onchange="jenis_kontrabon('ganti');" id="<?=$field?>2" name="<?=$field?>" value="Konsinyasi" />
															<label for="<?=$field?>2"> Konsinyasi </label>
														</div>
														<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
													</div>
												</div>
											</div>
											<div class="col-sm-6">
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-3">Tanggal Bayar</label>
													<div class="col-sm-6">
														<?php $field = 'tgl_bayar'; ?>
														<div class="input-group date">
															<div class="input-group-addon">
																<i class="fa fa-calendar"></i>
															</div>
															<input class="form-control pull-right datepicker_date_from" readonly onchange="update_tmp_master('m7', $(this).val()); validasi('<?=$field?>');" name="<?=$field?>" id="<?=$field?>" type="text" value="">
															<!--custom_front_date('pembelian', $(this).val())-->
														</div>
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-3">Supplier</label>
													<div class="col-sm-6">
														<?php $field = 'supplier' ?>
														<?php $option = null; $option[''] = 'Pilih';
														foreach($data_supplier as $row){ $option[$row['kode']] = $row['kode'].' | '.$row['Nama']; }
														echo form_dropdown($field, $option, set_value($field), array('class' => 'select2', 'required'=>'required', 'id'=>$field, 'onclick'=>'validasi(\''.$field.'\'); hapus_list_kontra(\'all\');', 'onchange'=>'update_tmp_master(\'m5\', $(this).val()); nilai_retur();')); ?>
														<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
														<b class="error" id="alr_<?=$field?>"></b>
													</div>
												</div>
												<div class="row" style="margin-bottom: 3px">
													<label class="col-sm-3">Keterangan</label>
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
							<div id="div_pembelian" class="row">
								<div class="col-md-12">
									<div class="panel-heading">
										<div class="row">
											<div class="col-md-2">
												<label>Nota Pembelian</label>
											</div>
											<div class="col-md-3">
												<div class="input-group">
													<?php $field='nota_beli'; ?>
													<input type="text" class="form-control input-sm" onkeyup="validasi('<?=$field?>'); if(event.keyCode==13){validasi('supplier'); validasi('tgl_bayar'); read_nota_beli($(this).val());}" id="<?=$field?>" name="<?=$field?>">
													<div class="input-group-btn">
														<button onclick="validasi('supplier'); validasi('tgl_bayar'); read_nota_beli();" type="button" id="cari_barcode" name="cari_barcode" class="btn btn-primary btn-sm"><i class="md md-search"></i></button>
													</div>
												</div>
												<b class="error" id="alr_nota_beli"></b>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div id="div_konsinyasi" class="row">
								<div class="col-md-12">
									<div class="panel-heading">
										<div class="row">
											<div class="col-md-2">
												<label>Periode Konsinyasi</label>
											</div>
											<div class="col-md-3">
												<div class="input-group">
													<?php $field = 'field-date';?>
													<div id="daterange" style="cursor: pointer;">
														<input type="text" name="<?=$field?>" id="<?=$field?>" class="form-control" style="height: 40px;" onkeyup="if(event.keyCode==13){validasi('supplier'); validasi('tgl_bayar'); read_nota_beli($(this).val(),'Konsinyasi');}">
													</div>
													<div class="input-group-btn">
														<button onclick="validasi('supplier'); validasi('tgl_bayar'); read_nota_beli($('#field-date').val(),'Konsinyasi'); nilai_retur();" type="button" id="cari_barcode" name="cari_barcode" class="btn btn-primary"><i class="md md-search"></i></button>
													</div>
												</div>
												<b class="error" id="alr_nota_beli"></b>
											</div>
										</div>
									</div>
								</div>
							</div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <thead>
												<tr>
													<th style="width: 10px;">No</th>
													<th width="50px">Action</th>
													<th id="det_nota_pembelian">Nota Pembelian</th>
													<th>Nota Supplier</th>
													<th>Tanggal Pembelian</th>
													<th>Tanggal Jatuh Tempo</th>
													<th id="det_nilai_pembelian">Nilai Pembelian</th>
													<th>Sudah Kontra Bon</th>
													<th>Nilai Kontra Bon</th>
												</tr>
                                            </thead>
                                            <tbody id="list_nota_beli_kontra">
												
                                            </tbody>
                                        </table>
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
                                        <label class="col-sm-4">Nilai Kontra Bon</label>
                                        <div class="col-sm-8">
                                            <input type="text" id="total" name="total" class="form-control text-right" readonly>
                                        </div>
                                    </div>
									<div class="row" style="margin-bottom: 3px">
                                        <label class="col-sm-4">Retur</label>
                                        <div class="col-sm-8">
											<input type="hidden" id="data_retur" name="data_retur" onchange="update_tmp_master('m13', $(this).val())" />
											<?php $field='retur'; ?>
                                            <input type="text" id="<?=$field?>" name="<?=$field?>" class="form-control text-right" onkeydown="return isNumber(event);" onkeyup="isMoney('<?=$field?>','+'); hitung_kontra(); validasi('<?=$field?>');" onblur="update_tmp_master('m8', $(this).val())" onchange="update_tmp_master('m8', $(this).val())" />
                                        	<b class="error" id="alr_<?=$field?>"></b>
										</div>
                                    </div>
									<div class="row" style="margin-bottom: 3px">
                                        <label class="col-sm-4">Biaya Adm.</label>
                                        <div class="col-sm-8">
                                        	<?php $field='adm'; ?>
                                            <input type="text" id="<?=$field?>" name="<?=$field?>" class="form-control text-right" onkeydown="return isNumber(event);" onkeyup="isMoney('<?=$field?>','+'); hitung_kontra(); validasi('<?=$field?>');" onblur="update_tmp_master('m9', $(this).val())" />
                                        </div>
                                    </div>
									<div class="row" style="margin-bottom: 3px">
                                        <label class="col-sm-4">Total</label>
										<div class="col-sm-8">
                                            <input type="text" id="grand_total" name="grand_total" class="form-control text-right" readonly>
                                        </div>
                                    </div>
									<div class="row" style="margin-bottom: 3px">
                                        <label class="col-sm-4">Pembayaran</label>
                                        <div class="col-sm-8">
                                        	<?php $field='pembayaran'; ?>
                                            <input type="text" id="<?=$field?>" name="<?=$field?>" class="form-control text-right" onkeydown="return isNumber(event);" onkeyup="isMoney('<?=$field?>','+'); hitung_pembulatan(); validasi('<?=$field?>');" onblur="update_tmp_master('m11', $(this).val())" onchange="update_tmp_master('m11', $(this).val())" readonly />
                                        </div>
                                    </div>
									<div class="row" style="margin-bottom: 3px">
                                        <label class="col-sm-4">Pembulatan</label>
										<div class="col-sm-1">
											<?php $field = 'cek_pembulatan'; ?>
											<div class="checkbox checkbox-primary">
												<input class="form-control" type="checkbox" onclick="($('#cek_pembulatan').is(':checked'))?(update_tmp_master('m10', '1')):(update_tmp_master('m10', '0')); hitung_pembulatan();" id="<?=$field?>" name="<?=$field?>" value="1" <?=(set_value($field)==1)?('checked'):null?> />
												<label for="<?=$field?>"></label>
											</div>
											<?=form_error($field, '<div class="error" style="color:red;">', '</div>')?>
										</div>
										<div class="col-sm-7">
											<?php $field='pembulatan'; ?>
											<input type="text" readonly id="<?=$field?>" name="<?=$field?>" class="form-control text-right" value="<?=set_value($field)?set_value($field):0?>" />
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
			<?= form_open_multipart($content, array('id'=>'form_nota_beli')) ?>
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h4 class="modal-title" id="modal-label">Supplier : <label id="lab_supplier"></label></h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<button style="margin-bottom:5px;" class="btn btn-primary" type="button" onclick="add_list_nota_beli_kontra();" id="add_kontra" name="add_kontra">Pilih</button>
							<table class="table table-striped table-bordered">
								<thead>
									<tr>
										<th style="width: 10px;">No</th>
										<th width="50px" style="text-align:center;">
											<div class="checkbox checkbox-primary">
												<input class="form-control" type="checkbox" id="checklist_nota" name="checklist_nota" />
												<label for="checklist_nota"></label>
											</div>
										</th>
										<th>Nota Pembelian</th>
										<th>Nota Supplier</th>
										<th>Tanggal Pembelian</th>
										<th>Tanggal Jatuh Tempo</th>
										<th>Nilai Pembelian</th>
									</tr>
								</thead>
								<tbody id="list_nota_beli">

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
	
	function after_change(val) {
		$.ajax({
			url: "<?php echo base_url().'site/set_session_date/' ?>" + btoa('field-date') + '/' + btoa(val),
			type: "GET"
		});
	}
	
	function get_tmp_master() {
        $.ajax({
            url: "<?= base_url() . $this->control . '/get_tr_temp_m_kb' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                if (data.status == 1) {
                    $('#tgl').datepicker("setDate", data.temp['m3']);
                    $("#descrip").val(data.temp['m6']).change();
					$("#lokasi").val(data.temp['m4']).change();
                    $("#supplier").val(data.temp['m5']).change();
					if(data.temp['m7']!=null && data.temp['m7']!=''){ $('#tgl_bayar').datepicker("setDate", data.temp['m7']); }
					$("#retur").val(data.temp['m8']).change();
					$("#data_retur").val(data.temp['m13']).change();
					$("#adm").val(data.temp['m9']).change();
					if(data.temp['m10']=='1'){ $('#cek_pembulatan').prop('checked', true); } else { $('#cek_pembulatan').prop('checked', false); }
					$("#pembayaran").val(data.temp['m11']).change();
					if(data.temp['m12']=='Konsinyasi'){ $('#jenis2').prop('checked', true); } else { $('#jenis1').prop('checked', true); }
				}
				jenis_kontrabon(); hitung_kontra();
            }
        });
    }
	
	function get_tmp_detail() {
        $.ajax({
            url: "<?= base_url() . $this->control . '/get_tr_temp_d_kb' ?>",
            type: "GET",
            dataType: "JSON",
            success: function (data) {
                $("#list_nota_beli_kontra").html(data.list_nota_beli_kontra);
                //$("#total").val(to_rp(data.total));
				setTimeout(function(){ hitung_kontra(); }, 500);
            }
        });
    }
	
	function update_tmp_master(column, value, trx='') {
        trx = 'KB';
		$.ajax({
			url: "<?= base_url() . $this->control . '/update_tr_temp_m_kb/' ?>" + btoa(trx) + "/" + btoa(column) + "/" + btoa(value),
			type: "GET"
		});
    }
	
	function update_tmp_detail(id, column, value) {
        $.ajax({
            url: "<?= base_url() . $this->control . '/update_tr_temp_d_kb/' ?>" + btoa(id) + "/" + btoa(column) + "/" + btoa(value),
            type: "GET"
        });
    }
	
	function read_nota_beli(nota='', jenis=''){ 
		$("#lab_supplier").text('');
		$("#alr_nota_beli").text('');
		var tgl_bayar = $('#tgl_bayar').val();
		var supplier = $('#supplier').val();
		//alert(nota + jenis);
		if(tgl_bayar!='' && supplier!=''){ 
			var data_supplier = <?=json_encode($data_supplier)?>;
			for(var i=0; i<data_supplier.length; i++){
				if(data_supplier[i]['kode']==supplier){
					$("#lab_supplier").text(data_supplier[i]['kode']+' | '+data_supplier[i]['Nama']);
					$("#checklist_nota").prop('checked', false)
					$.ajax({
						url: "<?= base_url() . $this->control . '/read_nota_beli_kb/' ?>" + btoa(supplier) + "/" + btoa(tgl_bayar) + "/" + btoa(nota) + "/" + btoa(jenis),
						type: "GET",
						dataType: "JSON",
						success: function (data) {
							$("#list_nota_beli").html(data.list_nota_beli);
							if(nota!=''){ 
								$('#checklist_nota1').prop('checked', true);
								$("#modal-container").modal('hide'); 
								$('#add_kontra').click();
								$('#nota_beli').val('');
								$('#field-date').val('');
								if(data.status == 0){ $("#alr_nota_beli").text('Nota Pembelian tidak ditemukan'); }
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
		/*for(var i=1; i<=$('#jumlah_nota_beli').val(); i++){
			if ($("#checklist_nota"+i).is(":checked")) {
				alert(i);
			}
		}*/
		
		$.ajax({
			url: "<?= base_url() . $this->control . '/add_list_kontra/' ?>",
			type: "POST",
			data: $('#form_nota_beli').serialize(),
			success: function(){ 
				get_tmp_detail();
				//setTimeout(function(){ $("#article"+id).focus(); }, 500); 
				$("#modal-container").modal('hide');
			}
		});
	}
	
	function hapus_list_kontra(id) {
        $.ajax({
            url: "<?= base_url() . $this->control . '/delete_tr_temp_d_kb/' ?>" + btoa(id),
            type: "GET",
            success: function (data) {
                if (data) {
                    get_tmp_detail();
                }
            }
        });
    }
	
	function jenis_kontrabon(aksi=''){
		var jenis;
		
		if($("#jenis1").is(":checked")){ 
			//jenis=$("#jenis1").val();	
			$("#field-date").val('');
			$("#div_pembelian").show();
			$("#div_konsinyasi").hide();
			$('#det_nota_pembelian').text('Nota Pembelian');
			$('#det_nilai_pembelian').text('Nilai Pembelian');
		} else { 
			//jenis=$("#jenis2").val();
			//$("#field-date").val('<?=date("Y/m/d")." - ".date("Y/m/d")?>');
			$("#div_pembelian").hide();
			$("#div_konsinyasi").show();
			$('#det_nota_pembelian').text('Periode Konsinyasi');
			$('#det_nilai_pembelian').text('Nilai Konsinyasi');
		}
		
		if(aksi=='ganti'){
			hapus_list_kontra('all');
		}
		
		nilai_retur();
	}
	
	function nilai_retur(){
		var supplier = $("#supplier").val();
		var tanggal = $("#tgl").val();
		//var periode = $("#field-date").val();
		var periode = '';
		
		/*if(tanggal!='' && supplier!=''){
			$.ajax({
				url: "<?= base_url() . $this->control . '/nilai_retur_kb/' ?>" + btoa(supplier) +"/"+ btoa(tanggal) +"/"+ btoa(periode),
				type: "GET",
				dataType: "JSON",
				success: function (data) {
					var retur = parseFloat(data.retur['nilai_retur']); if(data.retur['nilai_retur']==undefined){ retur = 0; }
					$("#retur").val(to_rp(retur)).change();
					$("#data_retur").val(to_rp(retur)).change();
					hitung_kontra();
				}
			});
		} else*/ {
			$("#retur").val(to_rp(0)).change();
			$("#data_retur").val(to_rp(0)).change();
			hitung_kontra();
		}
	}
	
	function hitung_kontra(){
		var sub_total=0;
		var total=0;
		var grand_total=0;
		var retur = hapuskoma($("#retur").val()); if(retur == ''){ retur = 0; };
		var adm = hapuskoma($("#adm").val()); if(adm == ''){ adm = 0; };

		for(var i=1; i<=$("#jumlah_nota_beli_kontra").val(); i++){ 
			if($("#nilai_kontrabon"+i).val() != undefined){
				sub_total = hapuskoma($("#nilai_kontrabon"+i).val()); if(sub_total == ''){ sub_total = 0; }
				total = total + parseFloat(sub_total);
			}
		}
		
		grand_total = (parseFloat(total) - parseFloat(retur)) + parseFloat(adm);
		
		$("#total").val(to_rp(total));
		$("#grand_total").val(to_rp(grand_total));
		if(isNaN(parseFloat(total))){ $("#simpan").prop("disabled", true); }
		else { $("#simpan").prop("disabled", false); }
		
		hitung_pembulatan();
	}
	
	function hitung_pembulatan(){
		var grand_total = $("#grand_total").val(); if(grand_total == ''){ grand_total = 0; };
		var jumlah_hutang = hapuskoma(grand_total); if(jumlah_hutang==''){ jumlah_hutang = 0; } jumlah_hutang = parseFloat(hapuskoma(jumlah_hutang));
		var jumlah_bayar = hapuskoma($("#pembayaran").val()); if(jumlah_bayar==''){ jumlah_bayar = 0; } jumlah_bayar = parseFloat(hapuskoma(jumlah_bayar));
		var bulat = jumlah_bayar - jumlah_hutang;
		
		if ($("#cek_pembulatan").is(":checked")) {
			//$("#cek_pembulatan").prop("checked", true);
			$("#pembayaran").prop("readonly", false);
			$("#pembulatan").val(to_rp(bulat));
		} else {
			$("#pembayaran").prop("readonly", true).val(grand_total).change();
			$("#pembulatan").val(0);
		}
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
            url: "<?= base_url() . $this->control . '/delete_trx_kb' ?>",
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
		
		if(column==''){ column='supplier'; } 
        if(column == 'supplier'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("Supplier harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; } 
		}
		
		if(column==''){ column='descrip'; } 
        if(column == 'descrip'){ 
			value = $("#"+column).val(); 
			if(value == ''){ $("#alr_"+column).text("Keterangan harus diisi!"); valid=0; } else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; }
		}
		
		for(var i=1; i<=$("#jumlah_nota_beli_kontra").val(); i++){ 
			if($("#nilai_kontrabon"+i).val() != undefined){
				var nilai_pembelian = hapuskoma($("#nilai_pembelian"+i).val()); if(nilai_pembelian == ''){ nilai_pembelian = 0; }
				var sudah_kontrabon = hapuskoma($("#sudah_kontrabon"+i).val()); if(sudah_kontrabon == ''){ sudah_kontrabon = 0; }
				var nilai_kontrabon = parseFloat(nilai_pembelian) - parseFloat(sudah_kontrabon);
				
				if(column==''){ column='nilai_kontrabon'+i; } 
				if(column == 'nilai_kontrabon'+i){ 
					value = hapuskoma($("#"+column).val()); 
					if(value == ''){ $("#alr_"+column).text("Nilai Kontrabon harus diisi!"); valid=0; } //else { $("#alr_"+column).text(""); } 
					else if(value <= 0){ $("#alr_"+column).text("Nilai Kontrabon harus lebih dari 0!"); valid=0; } //else { $("#alr_"+column).text(""); } 
					else if(parseInt(value) > parseInt(nilai_kontrabon)){ $("#alr_"+column).text("Maksimal Nilai Kontrabon " + to_rp(nilai_kontrabon)); valid=0; } else { $("#alr_"+column).text(""); } 
					if(action=='save'){ column=''; }
				}
			}
		}
		
		if(column==''){ column='retur'; } 
		if(column == 'retur'){
			var data_retur = parseFloat(hapuskoma($("#data_retur").val())); if(data_retur == ''){ data_retur = 0; };
			var total_kontra_bon = parseFloat(hapuskoma($("#total").val())); if(total_kontra_bon == ''){ total_kontra_bon = 0; };
			
			value = hapuskoma($("#"+column).val()); 
			if(value < 0){ $("#alr_"+column).text("Nilai Retur minimal 0!"); valid=0; } 
			//else if(value > data_retur){ $("#alr_"+column).text("Maksimal Nilai Retur " + to_rp(data_retur)); valid=0; } 
			else if(value > total_kontra_bon){ $("#alr_"+column).text("Melebihi kontra bon. Maksimal " + to_rp(total_kontra_bon)); valid=0; } 
			else { $("#alr_"+column).text(""); } 
			if(action=='save'){ column=''; }
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
					url: "<?= base_url().$this->control . '/simpan_kb' ?>",
					type: "POST",
					data: $('#form_tr').serialize(),
                    dataType: "JSON",
					success: function (data) {
                        if (data.status) {
                            //alert("Transaksi Berhasil!");
                            if (confirm('Transaksi Berhasil! Akan mencetak transaksi?')){
                                cetak_transaksi('kontra_bon_report', 'none', data.kode);
                            }
                        } else {
                            alert('Transaksi gagal');
                            location.reload();
                        }
					}
				});
			} else {
				alert('List cannot be empty!');
			}
        }
    }
</script>
